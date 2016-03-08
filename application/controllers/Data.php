<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Data extends CI_Controller {
	private $aes;
	private $data;

	public function __construct() {
		parent::__construct();
		$rsa = array(
			'pubkey' => '-----BEGIN RSA PUBLIC KEY----- MIGJAoGBANNrMM52kpQV1z0YKxK9pKOxWazj7IUpxPzAXXRAdaRd9qH0Jk8OtY6S /TXWPeUzF6VnReSUVMXlPpf9Idd8b/FhWhp5h3Eu+zlxv04OjNzwWdKpjhRNnG7c o+FSXfJ3B7H0udezRWar5Y1xxZgSXUG05q2d5FXJsClXzv80zDMJAgMBAAE= -----END RSA PUBLIC KEY-----', 
			'private' => '-----BEGIN RSA PRIVATE KEY----- MIICXgIBAAKBgQDTazDOdpKUFdc9GCsSvaSjsVms4+yFKcT8wF10QHWkXfah9CZP DrWOkv011j3lMxelZ0XklFTF5T6X/SHXfG/xYVoaeYdxLvs5cb9ODozc8FnSqY4U TZxu3KPhUl3ydwex9LnXs0Vmq+WNccWYEl1BtOatneRVybApV87/NMwzCQIDAQAB AoGAbCwgIMNSZCp4om3HPCOEJa0McQV9cvTYMWpLZrvEdYEOO/cr0q93/ab/n5gq uybVJnActsOeTFLrH+EIe7TToQMfQmTfZV5ZuE/Y2eQqBnxFQ+MQ3eDgLraUXl0B 8rx8uy4PnQBe8MEixE/kOT7FEOngNzDriiEiEpXiYOwdg7ECQQDzAaBEaKV6cJ0v S0kVFYryUXTS+H9RUvnqrubJMfGYM5oOgVHhNVF5qa/66U869vbWlcTomXGb6ar0 6L1szSP3AkEA3rkt+9E+HiYW6zKAjdH/dgyEvbh6zRcKrrBScbdheVrUW8VPXxZ0 5tbVmIJfO8KkSq/QHc2huRaRA8v5DR2g/wJBAJs+UrDhWbYa85AfPJUnqhicSVHu RwghRl/TVMT8Dyf471aM7048zcw3x6E4I9G7rH3yOFWQka/VW84SUdGMTIkCQQCT 8pK3KgGHaWnkBoIasxptQleS5067mcjAzeOWImifglR6OZFF6tbw2Fi+nCvCuMMF 0c//XC9HkdP2n7HqonnlAkEAhbCcdxzXZEfaWwEA78EQu5mEPkcnpkRnazUn3K5n IXSNrQ9FS5EanJbD6ZukKs251sZas37BEqI+Fcj0+iH+wA== -----END RSA PRIVATE KEY-----', 
			'publicHex' => 'd36b30ce76929415d73d182b12bda4a3b159ace3ec8529c4fcc05d744075a45df6a1f4264f0eb58e92fd35d63de53317a56745e49454c5e53e97fd21d77c6ff1615a1a7987712efb3971bf4e0e8cdcf059d2a98e144d9c6edca3e1525df27707b1f4b9d7b34566abe58d71c598125d41b4e6ad9de455c9b02957ceff34cc3309',
		);

		$keyShared = filter_input(INPUT_SERVER, 'HTTP_CONTENT_HEADER');

		if($keyShared) {
			$Phpseclib = new Phpseclib();
			$aesKeyPack = $Phpseclib->rsaDecryptCryptoJS($keyShared, $rsa);
			list($key, $iv) = explode('/', $aesKeyPack);
			$this->aes['key'] = $key;
			$this->aes['iv'] = $iv;
			// echo json_encode($this->aes);

			$data = json_decode(file_get_contents('php://input'), true);
			$this->data = json_decode($Phpseclib->aesDecryptCryptoJS($data['encrypted'], $this->aes['key'], $this->aes['iv']));
		}
	}

	// get link data
	public function get_link() {
		$Phpseclib = new Phpseclib();
		$data = array(
			'ok' => 1,
			'err' => null,
			'result' => array(
				'title' => 'Chỉ còn hơn 1 ngày nữa cuộc chiến giữa Apple và FBI sẽ phân thắng bại',
				'description' => 'Apple sắp phải đối mặt với Tòa án Tối cao để đưa ra lý lẽ và bằng chứng bảo vệ cho luận điểm của mình trước việc FBI yêu cầu mở khóa iPhone của kẻ sát nhân.',
				'origin' => $this->data->link,
				'images' => array(
					'https://genknews.vcmedia.vn/thumb_w/600/2016/1-image-1456390001213.jpg',
					'https://genknews.vcmedia.vn/k:2016/beaker-640x480-1456296357255/fbi-co-the-giai-ma-iphone-bi-khoa-bang-acid-va-tia-laser-nhu-the-nao.jpg',
					'https://genknews.vcmedia.vn/k:thumb_w/640/2016/ht-zonen-decapped-blur-lf-160221-4x3-992-1456294877018/fbi-co-the-giai-ma-iphone-bi-khoa-bang-acid-va-tia-laser-nhu-the-nao.jpg',
					'https://genknews.vcmedia.vn/k:thumb_w/640/2016/cxdwrhdm5dxpjt6zvuyg-1456124832555/fbi-co-the-giai-ma-iphone-bi-khoa-bang-acid-va-tia-laser-nhu-the-nao.jpg'
				),
			)
		);
		$response = $Phpseclib->aesEncryptCryptoJS(json_encode($data, true), $this->aes['key'], $this->aes['iv']);
		echo json_encode(array('response' => $response), true);
	}
	// search link
	public function search_link() {
		$this->data->page = $this->data->page?$this->data->page:1;
		$data = array(
			'ok' => 1,
			'err' => null,
			'keyword' => $this->data->keyword,
			'page' => $this->data->page,
			'result' => array()
		);

		for($count = 0; $count < 10; $count ++) {
			$data['result'][$count] = array(
				'title' => 'Title of test link ' . ($count * $this->data->page),
				'description' => 'Apple sắp phải đối mặt với Tòa án Tối cao để đưa ra lý lẽ và bằng chứng bảo vệ cho luận điểm của mình trước việc FBI yêu cầu mở khóa iPhone của kẻ sát nhân.',
				'origin' => 'http://test.gigido.vn/demo/test/'. ($count * $this->data->page),
				'images' => array(
					'https://genknews.vcmedia.vn/thumb_w/600/2016/1-image-1456390001213.jpg',
					'https://genknews.vcmedia.vn/k:thumb_w/640/2016/cxdwrhdm5dxpjt6zvuyg-1456124832555/fbi-co-the-giai-ma-iphone-bi-khoa-bang-acid-va-tia-laser-nhu-the-nao.jpg'
				),
				'avatar' => '1'
			);
		}
		$Phpseclib = new Phpseclib();
		$response = $Phpseclib->aesEncryptCryptoJS(json_encode($data, true), $this->aes['key'], $this->aes['iv']);
		echo json_encode(array('response' => $response), true);
	}
}