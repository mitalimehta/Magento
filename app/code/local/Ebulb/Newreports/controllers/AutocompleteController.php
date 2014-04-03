<?php
class Ebulb_Newreports_AutocompleteController extends Mage_Core_Controller_Front_Action   
{
    public function autocompleteAction()
    {   
       $searchModules = Mage::getConfig()->getNode("adminhtml/global_search");
      
        $items = array();

       /* if ( !Mage::getSingleton('admin/session')->isAllowed('admin/global_search') ) {
            $items[] = array(
                'id'=>'error',
                'type'=>'Error',
                'name'=>Mage::helper('adminhtml')->__('Access Deny'),
                'description'=>Mage::helper('adminhtml')->__('You have not enought permissions to use this functionality.')
            );
            $totalCount = 1;
        } else { */
            if (empty($searchModules)) {
                $items[] = array('id'=>'error', 'type'=>'Error', 'name'=>Mage::helper('adminhtml')->__('No search modules registered'), 'description'=>Mage::helper('adminhtml')->__('Please make sure that all global admin search modules are installed and activated.'));
                $totalCount = 1;
            } else {
                $start = $this->getRequest()->getParam('start', 1);
                $limit = $this->getRequest()->getParam('limit', 10);
                $query = $this->getRequest()->getParam('query', '');
                
                foreach ($searchModules->children() as $searchConfig) {
                  // echo "<pre>";print_r($searchConfig);
                   /* if ($searchConfig->acl && !Mage::getSingleton('admin/session')->isAllowed($searchConfig->acl)){
                        continue;
                    } */

                   $className = "Ebulb_Newreports_Model_Autocomplete"; //$searchConfig->getClassName();  
                   
                    if (empty($className)) {
                        continue;
                    }
                    $searchInstance = new $className();
                    $results = $searchInstance->setStart($start)->setLimit($limit)->setQuery($query)->load()->getResults();
                    $items = array_merge_recursive($items, $results);
                }
                $totalCount = sizeof($items);
            }
        //}
       
       if(count($items) > 0){
            echo "<ul>";
            foreach ($items as $item)
            {    
                $itemid               = $item['id']; 
                $itemsku              = $item['sku'];   
            
                    echo "<li id='$itemid' sku='$itemsku'>"; 
                    echo "<div style='font-weight:bold;' >".$itemsku."</div>";  
                    echo "</li>";  
            }
        
            echo "</ul>";
       }
        
       return;
    }
}