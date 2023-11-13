import {DeliverTxResponse} from "@cosmjs/stargate/build/stargateclient";
import {GasPrice} from "@cosmjs/stargate";
import {Network, SigningClient} from "@tedcryptoorg/cosmos-signer";
import {Chain, CosmosDirectory} from "@tedcryptoorg/cosmos-directory";
import {Account} from "./account";
import {BasicAllowance} from "cosmjs-types/cosmos/feegrant/v1beta1/feegrant";

export class Granter {
    private readonly restNodeAddress: string

    constructor (
        private readonly chain: Chain,
        private readonly account: Account,
        private readonly options: {
            defaultFee: string,
            defaultModifier: number
        },
        restNodeAddress?: string
    ) {
        this.restNodeAddress = restNodeAddress ?? new CosmosDirectory().restUrl(this.chain.name)
    }

    async getSignerClient (): Promise<SigningClient> {
        return new SigningClient(
            Network.createFromChain(this.chain, 60000, this.restNodeAddress).data,
            GasPrice.fromString(this.options.defaultFee),
            this.account.getSigner(),
            this.options.defaultModifier
        )
    }

    async grant (grantee: string): Promise<void> {
        const allowance = BasicAllowance.fromPartial({
            spendLimit: [
                {
                    denom: this.chain.denom,
                    amount: '1000000',
                }
            ],
            expiration: {
                seconds: BigInt(new Date().getTime() + 1000 * 60 * 60 * 24 * 365)
            },
        })

        const msg = {
            typeUrl: '/cosmos.feegrant.v1beta1.MsgGrantAllowance',
            value: {
                'granter': await this.account.getAddress(),
                'grantee': grantee,
                'allowance': {
                    'typeUrl': '/cosmos.feegrant.v1beta1.BasicAllowance',
                    'value': BasicAllowance.toJSON(allowance),
                }
            }
        }

        const account = await this.account.getAccount()
        const signerAddress = account.address
        let client
        try {
            client = await this.getSignerClient()
        } catch (error: any) {
            console.error('Couldn\'t connect to signer client! Error: ', error)
            throw new Error('Couldn\'t connect to signer client! Error: ' + String(error.message))
        }
        let gas
        try {
            // @ts-ignore: message is okay
            gas = await client.simulate(signerAddress, [msg], 'TedLotto - Paid prize')
            gas = Math.ceil(gas * this.options.defaultModifier)
        } catch (error) {
            console.log('Couldn\'t simulate! Error: ', error)
            gas = 200000
        }

        console.debug(`Gas: ${gas}`)

        const deliverTxResponse: DeliverTxResponse = await client.signAndBroadcast(
            signerAddress,
            // @ts-ignore: message is okay
            [msg],
            0,
            'Automated grant of fee allowance for Tedcrypto.io Tools'
        )

        console.log(`Broadcasted! height = ${deliverTxResponse.height}, txhash = ${deliverTxResponse.transactionHash}`)
    }
}