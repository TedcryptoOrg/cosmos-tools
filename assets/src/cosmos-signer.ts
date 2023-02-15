import { Network, SigningClient, Message } from '@tedcryptoorg/cosmos-signer'
import { ChainDirectory } from "@tedcryptoorg/cosmos-directory";
import { GasPrice } from "@cosmjs/stargate";

export default class CosmosSigner {
    private readonly signer: any

    constructor (signer: any) {
        this.signer = signer;
    }

    async getClient(chainSlug: string): Promise<SigningClient> {
        const chain = (await (new ChainDirectory(false)).getChainData(chainSlug));
        const network: Network = {
            chain_name: chain.name,
            authzAminoSupport: chain.params.authz,
            prefix: chain.bech32_prefix,
            txTimeout: 1200,
            coinType: chain.slip44,
            chainId: chain.chain_id,
        };

        return new SigningClient(network, GasPrice.fromString('1'+chain.denom), this.signer);
    }

    async sign (chainSlug: string, address:string, messages: Message[]): Promise<string>
    {
        const client = await this.getClient(chainSlug);
        let gasFee = 0;
        try {
            gasFee = await client.simulate(address, messages)
        } catch (error) {
            console.error(error);
            throw new Error('Failed to simulate gas fees. Please try again.')
        }

        return client.signAndBroadcast(address, messages, gasFee);
    }
}
