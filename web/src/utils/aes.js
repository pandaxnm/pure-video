import CryptoJS from 'crypto-js/crypto-js'

const key = CryptoJS.enc.Utf8.parse("1234123412ABCDEF");
const iv = CryptoJS.enc.Utf8.parse('ABCDEF1234123412');


export function Decrypt(data) {
    let decryptedData = CryptoJS.AES.decrypt(data, key, {
        iv: iv,
        mode: CryptoJS.mode.CBC,
        padding: CryptoJS.pad.Pkcs7
    });
    let decryptedStr = decryptedData.toString(CryptoJS.enc.Utf8);
    return decryptedStr.toString();
}

export function Encrypt(data) {
    let string = CryptoJS.enc.Utf8.parse(JSON.stringify(data));
    let encrypted = CryptoJS.AES.encrypt(string, key, { iv: iv, mode: CryptoJS.mode.CBC, padding: CryptoJS.pad.Pkcs7 });
    return encrypted.toString();
}


