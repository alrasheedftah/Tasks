<?php

class Travel
{
   private $travl_api='https://5f27781bf5d27e001612e057.mockapi.io/webprovise/travels';
   private $alltravel=[];

   function __construct() {
        $this->travels();
    }

   public function travels(){
    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => $this->travl_api,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "GET",
      CURLOPT_HTTPHEADER => array(
        "cache-control: no-cache"
      ),
    ));
    
    $response = curl_exec($curl);
    $err = curl_error($curl);
    
    curl_close($curl);
    
    $response = json_decode($response, true); //because of true, it's in an array
    $this->alltravel=  $response;
    
   }
   
   
   public function travels_cost($compID){
       $total_cost=0;
       foreach ($this->alltravel as $travel) {
           if($travel['companyId'] == $compID){
            $total_cost += $travel['price'];
           }
           # code...
       }
       return $total_cost;
   }

}

class Company
{
    private $company_ur='https://5f27781bf5d27e001612e057.mockapi.io/webprovise/companies';
    private $allCompanies=[];
    private $treeComp=[];

    function __construct() {
        $this->companies();
    }

    public function companies(){
        $curl = curl_init();
    
        curl_setopt_array($curl, array(
          CURLOPT_URL => $this->company_ur,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "GET",
          CURLOPT_HTTPHEADER => array(
            "cache-control: no-cache"
          ),
        ));
        
        $response = curl_exec($curl);
        $err = curl_error($curl);
        
        curl_close($curl);
        
        $response = json_decode($response, true); //because of true, it's in an array
        $this->allCompanies=  $response;
        
        // $this->companies_cost();


       }
    

       
       public function companies_cost(){
           $travel = new Travel();
           $companyCost=[];$i=0;
            foreach ($this->allCompanies as $company) {
                $company['cost']=$travel->travels_cost($company['id']);
                $companyCost[$i]=$company;
                $i++;
            }

            $this->allCompanies = $companyCost;

            $tree = $this->tree_companies(0);
            // echo json_encode($tree);
            return $tree;
       }


   public function tree_companies($parentID){

    $inParent=[];
    foreach ($this->allCompanies as $company) {
        // foreach ($this->allCompanies as $pare) {
        if($company['parentId'] == $parentID)
            {
                $children = $this->tree_companies($company['id']);
                if ($children) {
                    $company['children'] = $children;
                }
                $inParent[] = $company;
            }
        // }
    
   }
   
   return $inParent;

}



// Enter your code here
}

class TestScript
{
    public function execute()
    {
        $start = microtime(true);
        $company = new Company();
        $result = $company->companies_cost();
        echo json_encode($result);
        echo 'Total time: '.  (microtime(true) - $start);
    }
}

(new TestScript())->execute();


?>