<?php
#require_once realpath(dirname(__FILE__)) . '/../simpletest/test/autorun.php';
require_once('../simpletest/autorun.php');
require_once realpath(dirname(__FILE__)) . '/../lib/XMLFields.php';

class AllTests extends UnitTestCase{

	function test_simple_contact()
	{
		$hash = array(
		'firstName' =>'Greg',
		'lastName'=>'Formich',
		'companyName'=>'Litleco',
		'addressLine1'=>'900 chelmosford st',
		'city'=> 'Lowell',
		'state'=>'MA',
		'zip'=>'01831',
		'country'=>'US');
		$hash_out = XMLFields::contact($hash);
		$this->assertEqual($hash_out['firstName'],'Greg');
		$this->assertEqual($hash_out['addressLine2'], NULL);
		$this->assertEqual($hash_out['city'],'Lowell');
	}

	function test_simple_customerinfo()
	{
		$hash=array(
		'ssn'=>'5215435243',
	    'customerType'=>'monkey',
		'incomeAmount'=>'tomuchforamonkey',
		'incomeCurrency'=>'bannanas',
		'residenceStatus'=>'rent',
		'yearsAtResidence'=>'12'); 
		$hash_out = XMLFields::customerInfo($hash);
		$this->assertEqual($hash_out['ssn'],'5215435243');
		$this->assertEqual($hash_out['yearsAtEmployer'], NULL);
		$this->assertEqual($hash_out['incomeAmount'],'tomuchforamonkey');
	}

	function test_simple_BillMeLaterRequest()
	{
		$hash=array(
			'bmlMerchantId'=>'101',
		    'termsAndConditions'=>'none',
		    'preapprovalNumber'=>'000',
		    'merchantPromotionalCode'=>'test',
		    'customerPasswordChanged'=>'NO',
		    'customerEmailChanged'=>'NO');
		$hash_out = XMLFields::billMeLaterRequest($hash);
		$this->assertEqual($hash_out['bmlMerchantId'],'101');
		$this->assertEqual($hash_out['secretQuestionCode'], NULL);
		$this->assertEqual($hash_out['customerEmailChanged'],'NO');
	}

	function test_simple_fraudCheckType()
	{
		$hash=array(
		'authenticationValue'=>'123',
		'authenticationTransactionId'=>'123',
		'authenticatedByMerchant'=> "YES");
		$hash_out = XMLFields::fraudCheckType($hash);
		$this->assertEqual($hash_out['authenticationValue'],'123');
		$this->assertEqual($hash_out['customerIpAddress'], NULL);
		$this->assertEqual($hash_out['authenticationTransactionId'],'123');
	}

	function test_simple_authInformation()
	{
		#$hash['detailTax'] = array('avsResult' => '1234');
		$hash=array(
			'authDate'=>'123',
			'detailTax'=>(array('avsResult' => '1234')),
			'authAmount'=>'123');
	$hash_out = XMLFields::authInformation($hash);
	$this->assertEqual($hash_out['authDate'],'123');
	$this->assertEqual($hash_out['authCode'], 'REQUIRED');
	$this->assertEqual($hash_out['fraudResult']['avsResult'], '1234');
	$this->assertEqual($hash_out['fraudResult']['authenticationResult'], NULL);
	$this->assertEqual($hash_out['authAmount'],'123');
	}

	function test_simple_fraudResult()
	{
		$hash=array(
		'avsResult'=> '123',
		'ardValidationResult'=>'456',
		'advancedAVSResult'=>'789');
		$hash_out = XMLFields::fraudResult($hash);
		$this->assertEqual($hash_out['avsResult'],'123');
		$this->assertEqual($hash_out['authenticationResult'], NULL);
		$this->assertEqual($hash_out['advancedAVSResult'],'789');
	}

	function test_simple_healtcareAmounts()
	{
		$hash=array(
		'totalHealthcareAmount'=>'123',
		'RxAmount'=>'456',
		'visionAmount'=>'789');
		$hash_out = XMLFields::healthcareAmounts($hash);
		$this->assertEqual($hash_out['totalHealthcareAmount'],'123');
		$this->assertEqual($hash_out['dentalAmount'], NULL);
		$this->assertEqual($hash_out['RxAmount'],'456');
	}

	function test_simple_healtcareIIAS()
	{
		$hash=array(
		'healthcareAmounts'=>(array('totalHealthcareAmount'=>'123',
		'RxAmount'=>'456',
		'visionAmount'=>'789')),
		'IIASFlag'=>'456');
		$hash_out = XMLFields::healthcareIIAS($hash);
		$this->assertEqual($hash_out['healthcareAmounts']['totalHealthcareAmount'],'123');
		$this->assertEqual($hash_out['healthcareAmounts']['dentalAmount'], NULL);
		$this->assertEqual($hash_out['IIASFlag'],'456');
	}

	function test_simple_pos()
	{
		$hash=array(
		'capability'=>'123',
		'entryMode'=>'NO');
		$hash_out = XMLFields::pos($hash);
		$this->assertEqual($hash_out['capability'],'123');
		$this->assertEqual($hash_out['entryMode'], 'NO');
		$this->assertEqual($hash_out['cardholderId'],'REQUIRED');
	}

	function test_simple_detailTax()
	{
		$hash=array(
		'taxIncludedInTotal'=>'123',
		'taxAmount'=>'456',
		'taxRate'=>'high');
		$hash_out = XMLFields::detailTax($hash);
		$this->assertEqual($hash_out['taxIncludedInTotal'],'123');
		$this->assertEqual($hash_out['cardAcceptorTaxId'], NULL);
		$this->assertEqual($hash_out['taxAmount'],'456');
	}

	function test_simple_lineItemData()
	{
		$hash=array(
		'lineItemTotal'=>'1',
		'lineItemTotalWithTax'=>'2',
		'itemDiscountAmount'=>'3',
		'commodityCode'=>'3',
		'detailTax'=> (array('taxAmount' => 'high')));
		$hash_out = XMLFields::lineItemData($hash);
		$this->assertEqual($hash_out['lineItemTotal'],'1');
		$this->assertEqual($hash_out['unitCost'], NULL);
		$this->assertEqual($hash_out['lineItemTotalWithTax'],'2');
		$this->assertEqual($hash_out['detailTax']['taxAmount'],'high');
		$this->assertEqual($hash_out['detailTax']['taxRate'],NULL);
	}

	function test_simple_enhancedData()
	{
		$hash=array(
		'customerReference'=>'yes',
		'salesTax'=>'5',
		'deliveryType'=>'ups',
		'taxExempt'=>'no',
		'lineItemData' => (array('lineItemTotal'=>'1',
		'itemDiscountAmount'=>'3')),
		'detailTax'=> (array('taxAmount' => 'high')));
		$hash_out = XMLFields::enhancedData($hash);
		$this->assertEqual($hash_out['customerReference'], 'yes');
		$this->assertEqual($hash_out['lineItemData']['lineItemTotal'],'1');
		$this->assertEqual($hash_out['discountAmount'], NULL);
		$this->assertEqual($hash_out['lineItemData']['lineItemTotalWithTax'],NULL);
		$this->assertEqual($hash_out['detailTax']['taxAmount'],'high');
		$this->assertEqual($hash_out['detailTax']['taxRate'],NULL);
	}

	function test_simple_amexAggregatorData()
	{
		$hash = array(
		'sellerId'=>'1234');
		$hash_out = XMLFields::amexAggregatorData($hash);
		$this->assertEqual($hash_out['sellerId'], '1234');
		$this->assertEqual($hash_out['sellerMerchantCategoryCode'], NULL);
	
	}

}
?>

