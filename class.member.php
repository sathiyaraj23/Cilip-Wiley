<?php
class Member {

      public function __construct() {

          $arrConf = parse_ini_file("config.ini");
          
          $this->version        = $arrConf['version'];
          $this->endpoint       = $arrConf['endpoint'];
          $this->private_key    = $arrConf['private_key'];        
          $this->sa_passcode    = $arrConf['sa_passcode'];

          $this->return_url     = $arrConf['return_url'];
          $this->wiley_url      = $arrConf['wiley_url'];
          $this->target_url     = $arrConf['target_url'];
          $this->domain_url     = $arrConf['domain_url'];
          $this->DOI            = $arrConf['DOI'];
          
      }


      public function prepareTicketURL()
      {
        return $this->wiley_url.'?targetURL='.$this->target_url.$this->DOI.'&domain='.$this->domain_url.'&debug=true';
      }

      public function createPeopleFindIDRequest() {
       
              $input_xml = '<?xml version="1.0" encoding="utf-8"?>';
              $input_xml .='<YourMembership>
                          <Version>'.$this->version.'</Version>
                          <ApiKey>'.$this->private_key.'</ApiKey>
                          <CallID>001</CallID>
                          <SaPasscode>'.$this->sa_passcode.'</SaPasscode>
                            <Call Method="Sa.People.Profile.FindID">
                                <WebsiteID>'.$_REQUEST['wid'].'</WebsiteID>
                            </Call>
                        </YourMembership>';

              $profileid = $this->callAPI($input_xml);

              return $profileid;
      }

      public function createProfileGetRequest ($ProfileID) {

          $input_xml = '<?xml version="1.0" encoding="utf-8"?>';
          $input_xml .='<YourMembership>
                      <Version>'.$this->version.'</Version>
                      <ApiKey>'.$this->private_key.'</ApiKey>
                      <CallID>002</CallID>
                      <SaPasscode>'.$this->sa_passcode.'</SaPasscode>
                        <Call Method="Sa.People.Profile.Get">
                             <ID>'.$ProfileID.'</ID>
                        </Call>
                    </YourMembership>';

          $res = $this->callAPI($input_xml);
          
          return $res;
      }
      

      public function callAPI($input_xml) {
        
          $url      = $this->endpoint;

          $ch = curl_init();
          curl_setopt($ch, CURLOPT_URL, $url);
          curl_setopt($ch, CURLOPT_POSTFIELDS,"xmlRequest=" . $input_xml);
          curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
          curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 300);
          curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
          curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
          $data = curl_exec($ch);
          curl_close($ch);
          $arr = json_decode(json_encode(simplexml_load_string($data)), true);
          
          return $arr;
      }

      public function isValid() {
        
        $member = 0;
        $arr = $this->createPeopleFindIDRequest();

        if(isset($arr['Sa.People.Profile.FindID']['ID']) && !empty($arr['Sa.People.Profile.FindID']['ID'])) {
              
          $profDet = $this->createProfileGetRequest($arr['Sa.People.Profile.FindID']['ID']);

      //    $membertype = array("Complimentary","Student","Full","Affiliate","staff");
          
          // Suspended should be 0
          // Approved should be 1
          // Member type should match any of the following Complimentary,Student,Full,Affiliate,Staff
          // Member expiry date should be greater than or equal to today's date         
          if($profDet['Sa.People.Profile.Get']['Suspended'] == 0 && $profDet['Sa.People.Profile.Get']['Approved'] == 1 && $profDet['Sa.People.Profile.Get']['MembershipExpiry'] >= date("Y-m-d")) {
              
              $member = 1;
              return $member;    
          }
          else { 

              $member = 0;
              return $member;
          }
        } else { 

            $member = 0;
            return $member;
        }
      }


} 


?>