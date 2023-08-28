import { SigningClient, Message } from '@tedcryptoorg/cosmos-signer';
import { DeliverTxResponse } from "@cosmjs/stargate/build/stargateclient";
export default class CosmosSigner {
    private readonly signer;
    constructor(signer: any);
    getClient(chainSlug: string): Promise<SigningClient>;
    sign(chainSlug: string, address: string, messages: Message[], memo?: string | undefined): Promise<DeliverTxResponse>;
}
