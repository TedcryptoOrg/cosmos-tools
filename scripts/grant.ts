import {Granter} from "./granter";
import {bech32} from "bech32";
import {ChainDirectory} from "@tedcryptoorg/cosmos-directory";
import {Account} from "./account";

const args = process.argv.slice(2); // Exclude the first two elements (node binary and script name)
const chainDirectory = new ChainDirectory();

async function main() {
    const wallet = args[0];
    if (wallet === undefined) {
        throw new Error('Wallet address is required')
    }

    const prefix = bech32.decode(wallet).prefix;
    const prefixToChain: any = {
        'cosmos': 'cosmoshub',
        'osmo': 'osmosis',
    }
    const chain = (await chainDirectory.getChainData(prefixToChain[prefix] ?? prefix)).chain;
    const account = await Account.create(chain, args[1] ?? '')

    console.log('Grant wallet is: ' + await account.getAddress())

    const granter = new Granter(
        chain,
        account,
        {
            defaultFee: args[2] ?? '',
            defaultModifier: 1.5,
        }
    )

    await granter.grant(wallet)
}

main();