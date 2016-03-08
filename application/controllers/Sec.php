<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sec extends CI_Controller {
	private $rsa;

	public function __construct() {
		parent::__construct();
		$this->rsa = array(
			'pubkey' => '-----BEGIN RSA PUBLIC KEY----- MIGJAoGBANNrMM52kpQV1z0YKxK9pKOxWazj7IUpxPzAXXRAdaRd9qH0Jk8OtY6S /TXWPeUzF6VnReSUVMXlPpf9Idd8b/FhWhp5h3Eu+zlxv04OjNzwWdKpjhRNnG7c o+FSXfJ3B7H0udezRWar5Y1xxZgSXUG05q2d5FXJsClXzv80zDMJAgMBAAE= -----END RSA PUBLIC KEY-----', 
			'private' => '-----BEGIN RSA PRIVATE KEY----- MIICXgIBAAKBgQDTazDOdpKUFdc9GCsSvaSjsVms4+yFKcT8wF10QHWkXfah9CZP DrWOkv011j3lMxelZ0XklFTF5T6X/SHXfG/xYVoaeYdxLvs5cb9ODozc8FnSqY4U TZxu3KPhUl3ydwex9LnXs0Vmq+WNccWYEl1BtOatneRVybApV87/NMwzCQIDAQAB AoGAbCwgIMNSZCp4om3HPCOEJa0McQV9cvTYMWpLZrvEdYEOO/cr0q93/ab/n5gq uybVJnActsOeTFLrH+EIe7TToQMfQmTfZV5ZuE/Y2eQqBnxFQ+MQ3eDgLraUXl0B 8rx8uy4PnQBe8MEixE/kOT7FEOngNzDriiEiEpXiYOwdg7ECQQDzAaBEaKV6cJ0v S0kVFYryUXTS+H9RUvnqrubJMfGYM5oOgVHhNVF5qa/66U869vbWlcTomXGb6ar0 6L1szSP3AkEA3rkt+9E+HiYW6zKAjdH/dgyEvbh6zRcKrrBScbdheVrUW8VPXxZ0 5tbVmIJfO8KkSq/QHc2huRaRA8v5DR2g/wJBAJs+UrDhWbYa85AfPJUnqhicSVHu RwghRl/TVMT8Dyf471aM7048zcw3x6E4I9G7rH3yOFWQka/VW84SUdGMTIkCQQCT 8pK3KgGHaWnkBoIasxptQleS5067mcjAzeOWImifglR6OZFF6tbw2Fi+nCvCuMMF 0c//XC9HkdP2n7HqonnlAkEAhbCcdxzXZEfaWwEA78EQu5mEPkcnpkRnazUn3K5n IXSNrQ9FS5EanJbD6ZukKs251sZas37BEqI+Fcj0+iH+wA== -----END RSA PRIVATE KEY-----', 
			'publicHex' => 'd36b30ce76929415d73d182b12bda4a3b159ace3ec8529c4fcc05d744075a45df6a1f4264f0eb58e92fd35d63de53317a56745e49454c5e53e97fd21d77c6ff1615a1a7987712efb3971bf4e0e8cdcf059d2a98e144d9c6edca3e1525df27707b1f4b9d7b34566abe58d71c598125d41b4e6ad9de455c9b02957ceff34cc3309',
		);
	}

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */
	public function index()
	{
		
	}
	// ----------------------------------------------------------------

	/**
	 * RSA Key Init
	 */
	public function rsaPubKey() {
		// $rsa = new Crypt_RSA();

		// $rsa->setPrivateKeyFormat(CRYPT_RSA_PRIVATE_FORMAT_PKCS1);
  //       $rsa->setPublicKeyFormat(CRYPT_RSA_PUBLIC_FORMAT_PKCS1);
  //       $key = $rsa->createKey();
  //       $data['public'] = $key['publickey'];
  //       $data['private'] = $key['privatekey'];
  //       $rsa->loadKey($data['private']);
  //       $raw = $rsa->getPublicKey(CRYPT_RSA_PUBLIC_FORMAT_RAW);
  //       $data['publicHex'] = $raw['n']->toHex();
  //       foreach ($data as $key => $value) {
  //       	echo "'$key' => '$value', <br>";
  //       }
		echo json_encode(array('publicHex' => $this->rsa['publicHex']));
	}

	private function RSADecrypt() {
		$data = json_decode(file_get_contents('php://input'), true);
		$keyShared = filter_input(INPUT_SERVER, 'HTTP_CONTENT_HEADER');
		// $data['Content-Header'] = $keyShared;

		if(!empty($data['encrypted'])) {
			$rsa = new Crypt_RSA();
			$keyPack = pack('H*', $keyShared);
	        $rsa->loadKey($this->rsa['private']);
	        $rsa->setEncryptionMode(CRYPT_RSA_ENCRYPTION_PKCS1);
	        $aesKeyPack = $rsa->decrypt($keyPack);
	        list($aesKey1, $aesIvOrigin) = explode('/', $aesKeyPack);

	        $aes = new Crypt_AES();
	        $aes->setKey(pack('H*', $aesKey1));
	        $aes->setIV(pack('H*', $aesIvOrigin));

	        $data['descrypted'] = $aes->decrypt(base64_decode($data['encrypted']));

	        // $plaintext = rtrim( mcrypt_decrypt( MCRYPT_RIJNDAEL_128, base64_decode($aesKey), base64_decode($aesEncrypted), MCRYPT_MODE_CBC, base64_decode($aesIV)), "\t\0 " );
	        // var_dump($plaintext);
	        
	        $aes2 = new Crypt_AES();
	        $aes2->setKey(pack('H*', $aesKey1));
	        $aes2->setIV(pack('H*', $aesIvOrigin));

	        $data['response'] = base64_encode($aes->encrypt('ok'));
	        echo json_encode($data);
		}else {
			echo json_encode($data);
		}
        
	}
	
}
