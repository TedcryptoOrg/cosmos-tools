import { Network, SigningClient, Message } from '@tedcryptoorg/cosmos-signer'
import { ChainDirectory } from "@tedcryptoorg/cosmos-directory";
import { GasPrice } from "@cosmjs/stargate";
import {DeliverTxResponse} from "@cosmjs/stargate/build/stargateclient";

export default class CosmosSigner {
    private readonly signer: any

    constructor (signer: any) {
        this.signer = signer;
    }

    async getClient(chainSlug: string): Promise<SigningClient> {
        const chain = (await (new ChainDirectory(false)).getChainData(chainSlug)).chain;
        const networkData = Network.createFromChain(chain).data;
        networkData.txTimeout = 30000;

        return new SigningClient(networkData, GasPrice.fromString('1'+chain.denom), this.signer);
    }

    async sign (chainSlug: string, address:string, messages: Message[], memo?: string|undefined): Promise<DeliverTxResponse>
    {
        const client = await this.getClient(chainSlug);
        let gasFee = 0;
        try {
            gasFee = await client.simulate(address, messages)
        } catch (error) {
            console.error(error);
            throw new Error('Failed to simulate gas fees. Please try again.')
        }

        return client.signAndBroadcast(address, messages, gasFee, memo);
    }
}
