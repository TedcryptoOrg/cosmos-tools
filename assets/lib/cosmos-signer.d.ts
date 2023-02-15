import { SigningClient, Message } from '@tedcryptoorg/cosmos-signer';
export default class CosmosSigner {
    private readonly signer;
    constructor(signer: any);
    getClient(chainSlug: string): Promise<SigningClient>;
    sign(chainSlug: string, address: string, messages: Message[]): Promise<string>;
}
