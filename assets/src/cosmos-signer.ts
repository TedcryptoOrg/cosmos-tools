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
        return client.signAndBroadcast(address, messages, memo);
    }
}
