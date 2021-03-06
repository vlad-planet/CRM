<?php

namespace common\models;

use SoapClient;
use stdClass;

class RegHouse
{
	public $client;
	public $end_date;
	public static $conf;
	
	protected static $_instance;
	
	public function __construct(){
		$this->client = self::connectionСlient();
	}
	
	public static function getInstance() {
		self::$_instance = new self;  
		return self::$_instance;
	}
	
	static function connectionСlient()
	{
		self::$conf = Registrator::findByName('RegHouse');
		
		//$url = "https://panel.reghouse.ru:1443/partner_api.khtml?wsdl";
		$client = new SoapClient(self::$conf->api_url, array(
		"trace"      => 1,
		"exceptions" => 0));

		
		$loginresult = $client->logIn(self::$conf->login,self::$conf->password);
		$client->__setCookie('SOAPClient',$loginresult->status->message);
		
		return $client;
	}


	function getBalanceInfo(){
		$res = $this->client->getBalanceInfo();
		return $res;
	}

	function addDomain($domain, $ns){
		
		//$ns  = explode("\n", $model->ns);
		$nservers = $ns;
		$admin_o = 'admin_o';
		//$descr = ;
		$check_whois = -1;
		$hide_name_nichdl = 0;
		$hide_email = -1;
		$spam_process = 1;
		$hide_phone = 1;
        //$hide_phone_email = ;
		$years = 1;
		$registrar = 'RH';
		$dont_test_ns = 1;
		$ya_mail = 0;
		$purchase_privacy = 0;
		
		$res = $this->client->addDomains($domain, $nservers, $admin_o, $check_whois, $hide_name_nichdl, $hide_email, $spam_process, $hide_phone, $years, $registrar, $dont_test_ns, $ya_mail, $purchase_privacy);
		
		//echo '<pre>';
		//var_dump($res); exit;

		return $res;
	}

	function getDomain($domain)
	{
		$params=new stdClass();
		$params->domain = $domain;
		$params->state = 'ALL';
		$params->date_from = '';
		$params->date_to = '';
		$params->{'admin-o'} = self::$conf->user;
		$params->name_rus = 'Иван';
		$params->name_eng = 'Ivan';
		$params->isorg = 'PERSON';

		$strict = 0;
		$sort_field = 'domain';
		$sort_dir = 'asc';
		$limit = -1;

		$dmns = $this->client->getDomains($params,$strict, $sort_field, $sort_dir, $limit);

		$this->end_date = strtotime($dmns->data->domainarray[0]->{'reg-till'});

		$res = [];
		if(isset($dmns->data->domainarray[0]->name)){
			$res["Status"] = 'OK';
		}else{
			$res["Status"] .= "\n RegHous:\n ".$up_dns->status->message;
		}
		return $res; 
	}

	public function updateDNSDomain($domain,$ns=null)
	{
		//$user = 'OF_3754033-R01';

		if($ns != null){
			$ns = str_replace(" ", "\n", $ns);
		}else{
			$ns = "ns1.r01.ru\nns2.r01.ru";
		}

		$up_dns = $this->client->updateDomain($domain, $ns , self::$conf->user, '' , 1);
		
		$res["Status"] = $up_dns->status->name;
		
		if($up_dns->status->name != 'OK'){
			$res["Status"] .= "\n RegHous:\n ".$up_dns->status->message;
		}

		return $res;
	}

	public function addRecord($domain,$ip)
	{		
		$rec = $this->client->clearZone($domain);

		$params=new stdClass();
		$params->owner = '';
		$params->data = $ip;
		$params->pri = '';
		$params->weight = '';
		$params->port = '';
		$params->sshfp_algorithm = '';
		$params->sshfp_type = '';
		$params->info = '';

		$rec = $this->client->addNewRrRecord($domain,'A',$params);
			
		$params->owner = 'www';
		$rec = $this->client->addNewRrRecord($domain,'A',$params);
			
		$params->owner = '*';
		$rec = $this->client->addNewRrRecord($domain,'A',$params);

		$res["Status"] = $rec->status->name;
		
		if($rec->status->name != 'OK'){
			$res["Status"] .= "\n RegHous:\n ".$rec->status->message;
		}
		
		return $res;
	}
	
	public function requestStatus($domain)
	{
		return $this->end_date;
	}
	
	public function logOut()
	{
		$this->client->logOut();
		return true;
	}
	
}