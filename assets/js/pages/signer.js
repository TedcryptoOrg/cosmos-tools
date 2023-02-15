import CosmosSigner from "../../src/cosmos-signer";
import {KeplrWallet, WalletConnector} from "@tedcryptoorg/cosmos-wallet-wires";
import {ChainDirectory} from "@tedcryptoorg/cosmos-directory";
import {coin} from "@tedcryptoorg/cosmos-signer";
let { bech32 } = require('bech32')
import {PubKey} from "cosmjs-types/cosmos/crypto/ed25519/keys";

function getFormDataAsJson($form) {
    return $form.serializeArray().reduce(function(acc, {name, value}) {
        const parts = name.match(/(\w+)/g);
        parts.reduce((obj, property, index) => {
            if (index === parts.length - 1) {
                obj[property] = value;
            } else if (!obj[property]) {
                obj[property] = {};
            }
            return obj[property];
        }, acc);

        return acc;
    }, {});
}

$(document).ready(function() {
    $('#js-sign-button').click(async function (event) {
        event.preventDefault();

        const formData = getFormDataAsJson($(this).parentsUntil('form').parent()).signer;
        const network = formData.network;

        // Get chain data
        const chain = await (new ChainDirectory(false)).getChainData(network);

        // Enable Keplr and create a signer to it
        window.keplr.enable(chain.chain_id);
        const cosmosSigners = new CosmosSigner(window.getOfflineSigner(chain.chain_id));

        // Get address from Keplr
        const connector = new WalletConnector(
            new KeplrWallet(window.keplr, window.getOfflineSigner),
            chain.chain_id,
            {}
        );
        const address = await connector.getAddress();

        // Create message
        let messageData = formData.createValidatorForm;
        messageData.delegatorAddress = address;
        messageData.commission.rate = (messageData.commission.rate * 100000000000000000).toString();
        messageData.commission.maxRate = (messageData.commission.maxRate * 100000000000000000).toString();
        messageData.commission.maxChangeRate = (messageData.commission.maxChangeRate * 100000000000000000).toString();
        const { prefix, words } = bech32.decode(address);
        // Decode bech32 address and encode with bech "val"
        messageData.validatorAddress = bech32.encode(prefix + "valoper", words);
        messageData.pubkey = {
            typeUrl: "/cosmos.crypto.ed25519.PubKey",
            value: PubKey.encode({
                key: new Uint8Array(Buffer.from(messageData.pubkey, 'base64')),
            }).finish()
        };
        messageData.value = coin(messageData.value, chain.denom);

        const Message = [
            {
                "typeUrl": "/" + formData.typeUrl,
                'value': messageData
            }
        ]

        await cosmosSigners.sign(network, address, Message);
    });
});