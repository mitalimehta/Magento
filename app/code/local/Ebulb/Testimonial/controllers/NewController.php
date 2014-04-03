<?php

class Ebulb_Testimonial_NewController extends Mage_Core_Controller_Front_Action
{
	public function indexAction()
	{
		/*$user = Mage::getSingleton('customer/session');
		if($user->isLoggedIn())
		{
			echo $user->getCustomer()->getName();
			echo $user->getBillingAddress()->getCity();
			exit();
		}*/
		$this->loadLayout();     
		$this->renderLayout();
	}
	
	public function saveAction() {
		
 	if ($data = $this->getRequest()->getPost()) {	
		/*if(isset($_FILES['filename']['name']) && $_FILES['filename']['name'] != '') {
		try {	
			// Starting upload 	
			$uploader = new Varien_File_Uploader('filename');
			
			// Any extention would work
	   		$uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
			$uploader->setAllowRenameFiles(false);
					
			// Set the file upload mode 
			// false -> get the file directly in the specified folder
			// true -> get the file in the product like folders 
			//	(file.jpg will go in something like /media/f/i/file.jpg)
			$uploader->setFilesDispersion(false);
					
			// We set media as the upload dir
			$path = Mage::getBaseDir('media') . DS ;
			$uploader->save($path, $_FILES['filename']['name'] );
					
			} catch (Exception $e) {
		      
		    }
	        
		        //this way the name is saved in DB
	  		$data['filename'] = $_FILES['filename']['name'];
		}*/
	  			
	
			$model = Mage::getModel('testimonial/testimonial');
					
			$model->setData($data)
				->setId($this->getRequest()->getParam('id'));

			try {
				if ($model->getCreatedTime == NULL || $model->getUpdateTime() == NULL) {
					$model->setCreatedTime(now())
						->setUpdateTime(now());
				} else {
					$model->setUpdateTime(now());
				}	
				
				$model->save();
				Mage::getSingleton('core/session')->addSuccess(Mage::helper('testimonial')->__('Thank you for submitting a Testimonial.'));
				Mage::getSingleton('core/session')->setFormData(false);

				if ($this->getRequest()->getParam('back')) {
					$this->_redirect('*/*/edit', array('id' => $model->getId()));
					return;
				}
				$this->_redirect('*/*/');
				return;
            } catch (Exception $e) {
                Mage::getSingleton('core/session')->addError($e->getMessage());
                Mage::getSingleton('core/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('core/session')->addError(Mage::helper('testimonial')->__('Unable to find testimonial to save'));
        $this->_redirect('*/*/');
	}
	
	
}