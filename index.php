<?php 
    require 'simplehtmldom/simple_html_dom.php';

    $html = file_get_html('https://clutch.co/ca/developers/toronto');   

    $prourl = $state_country = $weburl = $lineData = array();
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

    // echo "<pre>" ;
    // print_r($prourl);
    // die;

    
    // foreach($prourl as $val) { 
        $htmlpro = file_get_html('https://clutch.co/profile/harplabs');    
        $lineData['company_name'] = $htmlpro->find('h1.header-company--title a.website-link__item', 0);
        $lineData['phone_number'] = $htmlpro->find('a.phone_icon', 0);    
        $lineData['reviewer'] = $htmlpro->find('div.field-name-full-name-display div', 0);
        $lineData['title'] = $htmlpro->find('div.field-name-title div.field-item', 0);
        $lineData['project_name'] = $htmlpro->find('a.inner_url', 0);
        $lineData['project_summary'] = $htmlpro->find('div.field-name-proj-description div.field-item p', 0);
        $lineData['business_type'] = $htmlpro->find('div.field-name-project-type div.field-item span', 0);
        $lineData['budget_spent'] = $htmlpro->find('div.field-name-cost div.field-item', 0);
        $lineData['project_validity'] = $htmlpro->find('div.field-name-project-length div.field-item', 0);
        $lineData['team_size'] = $htmlpro->find('div.field-name-company-size span.field-item', 0);  
        $lineData['state_country'] = $htmlpro->find('div.field-name-location span.field-item', 0); 
        
        echo "<pre>";
        print_r($lineData);
        die;
    // }    

     // Excel file name for download 
     $fileName = "codexworld_export_data-.xlsx"; 
    
     $fields = array('Review for company- Name & URL', 'Company Name', 'Website/URL *', 'Email *', 'Phone number *', 'Reviewer Name ', 'Title (Domain)', 'Project Name', 'Project summary',
    'Business type', 'Budget/spent amount', 'Project Validity (Datewise)', 'Is project on-going?', 'Team Size', 'State/Country', 'LinkedIn URL 1', 'LinkedIn URL2', 'Review for company- URL', 'Other info/Urls'); 
     
     // Display column names as first row 
     $excelData = implode("\t", array_values($fields)) . "\n"; 
     $lineData = array();

     $excelData .= implode("\t", array_values($lineData)) . "\n"; 
 
     // Headers for download 
     header("Content-Type: application/vnd.ms-excel"); 
     header("Content-Disposition: attachment; filename=\"$fileName\""); 
 
     // Render excel data 
     echo $excelData;  
     exit; 


   
    
?>  