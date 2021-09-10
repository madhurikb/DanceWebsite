<?php 
    set_time_limit(0);
    ini_set('max_execution_time', '300');
     // Filter the excel data 
    function filterData(&$str){ 
        $str = preg_replace("/\t/", "\\t", $str); 
        $str = preg_replace("/\r?\n/", "\\n", $str); 
        if(strstr($str, '"')) $str = '"' . str_replace('"', '""', $str) . '"'; 
    }

    require 'simplehtmldom/simple_html_dom.php';

    $html = file_get_html('https://clutch.co/ca/developers/toronto');   

    $prourl = $state_country = $weburl = $lineData_arr = array();
    foreach($html->find('a') as $element) {
        if ($element->class == 'company_logotype') {  
            $prourl[] = 'https://clutch.co'.$element->href; 
        } 
        if ($element->class == 'website-link__item') { 
            $weburl[] = $element->href;
        } 
    }  

    foreach($html->find('span') as $element) {
        if ($element->class == 'locality') {  
            $state_country[] = $element->plaintext;
        }  
    }

    unset($prourl['41']);   

    // echo "<pre>";
    // print_r($prourl);

    // print_r($weburl);

    // echo count($prourl);
    // echo "<br />".count($weburl);
    // echo "<br />".count($state_country);
    // exit();


     // Excel file name for download 
     $fileName = "export_data.xlsx";    
    
     $fields = array('Review for company- Name & URL', 'Company Name', 'Website/URL *', 'Email *', 'Phone number *', 'Reviewer Name ', 'Title (Domain)', 'Project Name', 'Project summary', 'Business type', 'Budget/spent amount', 'Project Validity (Datewise)', 'Is project on-going?', 'Team Size', 'State/Country', 'LinkedIn URL 1', 'LinkedIn URL2', 'Review for company- URL', 'Other info/Urls');   
     
     // Display column names as first row 
     $excelData = implode("\t",array_values($fields)) . "\r\n"; 

    $i=0;
    foreach($prourl as $val) { 
        $htmlpro = file_get_html($val);          
        $company_name_h = $htmlpro->find('h1.header-company--title a.website-link__item', 0);
        $phone_number_h = $htmlpro->find('a.phone_icon', 0);    
        $reviewer_h = $htmlpro->find('div.field-name-full-name-display div', 0);
        $title_h = $htmlpro->find('div.field-name-title div.field-item', 0);
        $project_name_h = $htmlpro->find('a.inner_url', 0);
        $project_summary_h = $htmlpro->find('div.field-name-proj-description div.field-item p', 0);
        $business_type_h= $htmlpro->find('div.field-name-project-type div.field-item span', 0);
        $budget_spent_h = $htmlpro->find('div.field-name-cost div.field-item', 0);
        $project_validity_h = $htmlpro->find('div.field-name-project-length div.field-item', 0);
        $team_size_h = $htmlpro->find('div.field-name-company-size span.field-item', 0);

        $company_name = $company_name_h?$company_name_h->plaintext:'NA';
        $phone_number = $phone_number_h?$phone_number_h->plaintext:'NA';
        $reviewer = $reviewer_h?$reviewer_h->plaintext:'NA';
        $title = $title_h?$title_h->plaintext:'NA';
        $project_name = $project_name_h?$project_name_h->plaintext:'NA';
        $project_summary = $project_summary_h?$project_summary_h->plaintext:'NA'; 
        $business_type = $business_type_h?$business_type_h->plaintext:'NA';
        $budget_spent = $budget_spent_h?$budget_spent_h->plaintext:'NA';
        $project_validity = $project_validity_h?$project_validity_h->plaintext:'NA';
        $team_size = $team_size_h?$team_size_h->plaintext:'NA';

        $lineData_arr = array($company_name, $company_name, $weburl[$i], 'NA' , $phone_number, $reviewer, $title, $project_name, $project_summary, $business_type, $budget_spent, $project_validity, 'NA' , $team_size, $state_country[$i], 'NA', 'NA', $val, $val); 
         
        array_walk($lineData_arr, 'filterData');
        $excelData .= implode("\t",array_values($lineData_arr)) . "\r\n";  
        $i++;
    }       

     // Headers for download 
     header("Content-Type: application/vnd.ms-excel"); 
     header("Content-Disposition: attachment; filename=\"$fileName\""); 
 
     // Render excel data 
     echo $excelData;  
     exit;  
    
?>  