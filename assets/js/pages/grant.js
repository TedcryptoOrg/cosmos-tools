import {KeplrWallet, WalletConnector} from "@tedcryptoorg/cosmos-wallet-wires";
import {bech32} from "bech32";
import {ChainDirectory} from "@tedcryptoorg/cosmos-directory";
import CosmosSigner from "../../src/cosmos-signer";
import {MsgRevoke} from "cosmjs-types/cosmos/authz/v1beta1/tx";

let walletConnector = null;

function getWalletConnector(chainId) {
    if (walletConnector === null) {
        walletConnector = new WalletConnector(
            new KeplrWallet(window.keplr, window.getOfflineSigner),
            chainId,
            {}
        );
    }

    return walletConnector;
}

function getAddressInForm() {
    return $('#js-grant-form').find('input').val();
}

async function getChainFromWallet(wallet) {
    const prefix = bech32.decode(wallet).prefix;

    const prefixToChain = {
        'cosmos': 'cosmoshub',
        'osmo': 'osmosis',
    }

    return (await (new ChainDirectory().getChainData(prefixToChain[prefix] ?? prefix))).chain;
}

async function isMyWallet(wallet, chain) {
    const connector = getWalletConnector(chain.chain_id)
    const address = await connector.getAddress();

    return address === wallet;
}

/**
 * @param {Chain} chain
 * @param {string} txHash
 */
function getExplorerUrl(chain, txHash) {
    if (chain.explorers.length === 0) {
        return
    }
    let url = chain.explorers[0].tx_page.replace('${txHash}', txHash);
    chain.explorers.forEach((explorer) => {
        if (explorer.kind === 'mintscan') {
            url = explorer.tx_page.replace('${txHash}', txHash);
        }
    })

    return url;
}

$(document).ready(function() {
    $('#js-request-grant').click(async function (event) {
        event.preventDefault();
        const $this = $(this);
        const grantee = getAddressInForm();
        const chain = await getChainFromWallet(grantee);
        const userWallet = await getWalletConnector(chain.chain_id).getAddress();
        if (!await isMyWallet(grantee, chain)) {
            alert(
                'Wallet "%s" is not your wallet. Your wallet is "%s"'
                    .replace('%s', grantee)
                    .replace('%s', userWallet)
            );
            return;
        }

        const defaultMessage = $(this).html();
        $(this).html('Requesting...');
        $(this).attr('disabled', true);

        // serialise form and send to different endpoint using ajax
        $.ajax({
            type: 'POST',
            url: '/cosmos/grants/request',
            data: $('#js-grant-form').serialize(),
            success: function (data) {
                console.log(data);
                window.notifier.success('Request success!');
            },
            error: function (data) {
                console.log(data);
                window.notifier.alert(data.responseJSON.message);
            },
            complete: function () {
                $this.html(defaultMessage);
                $this.attr('disabled', false);
            }
        });
    });

    $('#js-revoke-btn').click(async function (event) {
        const grantee = $(this).data('grantee');
        const granter = $(this).data('granter');
        const chain = await getChainFromWallet(granter);
        const userWallet = await getWalletConnector(chain.chain_id).getAddress();
        if (!await isMyWallet(granter, chain)) {
            alert(
                'Wallet "%s" is not your wallet. Your wallet is "%s"'
                    .replace('%s', granter)
                    .replace('%s', userWallet)
            );
            return;
        }

        const msg = {
            typeUrl: '/cosmos.authz.v1beta1.MsgRevoke',
            value: MsgRevoke.fromJSON({
                'granter': granter,
                'grantee': grantee,
                'msgTypeUrl': $(this).data('msg'),
            })
        }
        console.log(msg);

        const signer = new CosmosSigner(window.getOfflineSigner(chain.chain_id));
        try {
            const response = await signer.sign(chain.chain_name, userWallet, [msg], 'Revoked by Tedcrypto.io Tools https://tools.tedcrypto.io')
            console.log(response);
            const explorerUrl = getExplorerUrl(chain, response.transactionHash);

            window.notifier.success('Revoke success! Tx: <a href="%s" target="_blank">%s</a>'.replaceAll('%s', explorerUrl).replaceAll('%s', response.transactionHash));
        } catch (error) {
            console.log(error);
            window.notifier.alert(error.message);
        }
    });
})
