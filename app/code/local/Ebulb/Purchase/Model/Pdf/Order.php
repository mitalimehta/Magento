<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @copyright  Copyright (c) 2009 Maison du Logiciel (http://www.maisondulogiciel.com)
 * @author : Olivier ZIMMERMANN
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
/**
 * Classe pour l'impression d'un bon de commande fournisseur
 *
 */
class Ebulb_Purchase_Model_Pdf_Order extends Ebulb_Purchase_Model_Pdf_Pdfhelper
{
	
	private $_showPictures = false;
	private $_pictureSize = 70;
	
	public function getPdf($orders = array())
    {
        
        $this->_beforeGetPdf();
        $this->_initRenderer('invoice');

        
        
        if ($this->pdf == null)
	        $this->pdf = new Zend_Pdf();
	    else 
	    	$this->firstPageIndex = count($this->pdf->pages);
	    
        $style = new Zend_Pdf_Style();
        $style->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA_BOLD), 10);

        foreach ($orders as $order) 
        {

            
	        //cree la nouvelle page
	        $titre = Mage::helper('purchase')->__('Purchase Order');
	        $settings = array();
	        $settings['title'] = $titre;
	        $settings['store_id'] = $order->getStoreId();
	        $page = $this->NewPage($settings);
			
            //cartouche
	        //$txt_date = "Date :  ".mage::helper('core')->formatDate($order->getCreatedAt(), 'long');
	        $txt_date = "Date :  ".date('d/m/Y', strtotime($order->getCreatedDate()));
	        $txt_order = "No ".$order->getIncrementId();
	        $adresse_fournisseur = $order->getVendor()->getAddressAsText();
	        //$adresse_client = Mage::getStoreConfig('sales/identity/address',$order->getpo_store_id());
            $adresse_client = $order->getShippingAddressAsText();
    
            $this->AddAddressesBlock($page, $adresse_fournisseur, $adresse_client, $txt_date, $txt_order);
            
            //affiche l'entete du tableau
            $this->drawTableHeader($page);
            
            //affiche l'entete du tableau
            $this->drawTableItems($page, $order, $style, $settings);
            
            //si on a plus la place de rajouter le footer, on change de page
        	if ($this->y < (150))
        	{
        		$this->drawFooter($page,$order);
        		$page = $this->NewPage($settings);
        		$this->drawTableHeader($page);
        	}
	        	
			//barre grise début totaux
            
	        $this->y -= 10;
	        $page->setLineWidth(1.5);
            $page->drawLine(10, $this->y, $this->_BLOC_ENTETE_LARGEUR,  $this->y);
	        
	        //barre verticale de séparation des totaux
	        $VerticalLineHeight = 80;
	        $page->drawLine($this->_PAGE_WIDTH / 2, $this->y, $this->_PAGE_WIDTH / 2, $this->y - $VerticalLineHeight);
	        
	        //rajoute les libellés & les totaux
	        $page->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA), 14);
	        $page->setFillColor(new Zend_Pdf_Color_GrayScale(0.0));
	        $this->y -= 20;
	                	            	
	    	//Zone commentaires
	    	$comments = $order->getComments();
			if (($comments != '') && ($comments != null))
	    	{
		        //$page->setFillColor(new Zend_Pdf_Color_GrayScale(0.3));
		        $page->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA), 12);
		        $page->drawText(mage::helper('purchase')->__('Order comments'), 15, $this->y, 'UTF-8');
		        //$offset = $this->DrawMultilineText($page, $com, 15, $this->y - 15, 10, 0.3, 11);
		        //$this->drawTextInBlock($page, $com, 15, $this->y - 15, 200, 200, 'l');
		        $comments = $this->WrapTextToWidth($page, $comments, $this->_PAGE_WIDTH / 2);
		        $this->DrawMultilineText($page, $comments, 15, $this->y - 15, 10, 0.2, 11);
	    	}
	        if ($order->getStatus() != Ebulb_Purchase_Model_Order::STATUS_NEW )
	        {
		        $page->drawText(mage::helper('purchase')->__('Subtotal'), $this->_PAGE_WIDTH / 2 + 10, $this->y, 'UTF-8');
		        $this->drawTextInBlock($page, $order->getCurrency()->formatTxt($order->getSubtotal()), $this->_PAGE_WIDTH / 2, $this->y, $this->_PAGE_WIDTH / 2 - 30, 40, 'r');   
				$this->y -= 20;
		        $page->drawText('Tax', $this->_PAGE_WIDTH / 2 + 10, $this->y, 'UTF-8');
		        $this->drawTextInBlock($page, $order->getCurrency()->formatTxt($order->getTax()), $this->_PAGE_WIDTH / 2, $this->y, $this->_PAGE_WIDTH / 2 - 30, 40, 'r');   
		        $this->y -= 20;
		        $page->drawText('Grand Total', $this->_PAGE_WIDTH / 2 + 10, $this->y, 'UTF-8');
		        $this->drawTextInBlock($page, $order->getCurrency()->formatTxt($order->getTotal()), $this->_PAGE_WIDTH / 2, $this->y, $this->_PAGE_WIDTH / 2 - 30, 40, 'r');   
	        }
	        	        
	        //barre grise fin totaux
	        $this->y -= 20;
	        $page->drawLine(10, $this->y, $this->_BLOC_ENTETE_LARGEUR,  $this->y);
	        
	        //Rajoute le réglement et le transporteur
	        $page->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA), 10);
	        $this->y -= 20;
	        $page->drawText(Mage::helper('purchase')->__('Payment Method : %s', $order->getPaymentTerms()), 15, $this->y, 'UTF-8');
            //$order->getpo_payment_type()
	        $this->y -= 20;
	        $page->drawText(Mage::helper('purchase')->__('Shipping Method : %s', $order->getShippingMethod()), 15, $this->y, 'UTF-8');
	        //$order->getpo_carrier()
            
	        //ligne de séparation
	        $this->y -= 20;
	        $page->drawLine(10, $this->y, $this->_BLOC_ENTETE_LARGEUR,  $this->y);
	        
	        //Zone acceptation de la commande
	        $this->y -= 20;
	        $page->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA), 12);
	        $page->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA), 10);
	        $txt = Mage::getStoreConfig('purchase/general/pdf_comment',$order->getStoreId());
	        $txt = $this->WrapTextToWidth($page, $txt, $this->_PAGE_WIDTH - 100);
	        $this->DrawMultilineText($page, $txt, 15, $this->y, 10, 0.2, 11);
	       
            //dessine le pied de page
	        $this->drawFooter($page,$order);
        }
        
        //rajoute la pagination
        $this->AddPagination($this->pdf);
        
        $this->_afterGetPdf();

        return $this->pdf;
    }
    
	 
	 /**
	  * Dessine l'entete du tableau avec la liste des produits
	  *
	  * @param unknown_type $page
	  */
                                                            
     public function drawTableItems(&$page,$order,&$style,&$settings)
     {
            $this->y +=2;
            
            //Affiche les lignes produit
            foreach ($order->getOrderItems() as $item)
            {

                //Pour les produits "standards"
                $page->setFillColor(new Zend_Pdf_Color_GrayScale(0.2));
                $page->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA), 6);
                
                //display SKU or picture
                if ($this->_showPictures == false)
                {
                    $page->drawText($this->TruncateTextToWidth($page, Mage::getModel('purchase/vendorproduct')->loadByProductId($order->getVendorId(), $item->getProductId())->getVendorSku(), 60), 12, $this->y, 'UTF-8');
                    $page->drawText($this->TruncateTextToWidth($page, $item->getSku(), 60), 65, $this->y, 'UTF-8');
                }
                else 
                {
                    $product = Mage::getModel('catalog/product')->load($item->getProductId());
                    if ($product->getId())
                    {
                        $productImagePath = Mage::getBaseDir().'/media/catalog/product'.$product->getsmall_image();
                        if (is_file($productImagePath)) 
                        {
                            try 
                            {
                                $image = Zend_Pdf_Image::imageWithPath($productImagePath);
                                $page->drawImage($image, 10, $this->y-$this->_pictureSize+20, 5+$this->_pictureSize, $this->y+10);                                
                            }
                            catch (Exception $ex)
                            {
                                
                            }
                        }
                    }
                }
                $page->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA), 7);
                $caption = $this->WrapTextToWidth($page, $item->getProductName(), 200);
                $offset = $this->DrawMultilineText($page, $caption, 122, $this->y, 7, 0.2, 8);
                if ($order->getStatus() != Ebulb_Purchase_Model_Order::STATUS_NEW )
                    $this->drawTextInBlock($page, $order->getCurrency()->formatTxt($item->getProductPrice()), 332, $this->y, 60, 20, 'r');   
                $this->drawTextInBlock($page, (int)$item->getProductQty(), 393, $this->y, 30, 20, 'r');   
                if ($order->getStatus() != Ebulb_Purchase_Model_Order::STATUS_NEW )
                {
                    $this->drawTextInBlock($page, number_format($item->getTaxRate(), 2).'%', 440, $this->y, 40, 20, 'c');     
                    $this->drawTextInBlock($page, $order->getCurrency()->formatTxt($item->getSubtotal()), 465, $this->y, 60, 20, 'r');     
                    $this->drawTextInBlock($page, $order->getCurrency()->formatTxt($item->getTotal()), 520, $this->y, 60, 20, 'r');   
                }
                
                if ($this->_showPictures)
                       $this->y -= $this->_pictureSize;                    
                else
                       $this->y -= $this->_ITEM_HEIGHT;
                                   
                //si on a plus la place de rajouter le footer, on change de page
                if ($this->y < ($this->_BLOC_FOOTER_HAUTEUR + 40))
                {
                    $this->drawFooter($page,$order);
                    $page = $this->NewPage($settings);
                    $this->drawTableHeader($page);
                }
                
                $line_start  = $this->y+30;
                $line_height = $this->_ITEM_HEIGHT+18;
                $page->setLineWidth(0.5);
                $page->drawLine(63,  $line_start, 63,   $this->y-$line_height);
                $page->drawLine(118, $line_start, 118,  $this->y-$line_height);
                $page->drawLine(343, $line_start, 343,  $this->y-$line_height);
                $page->drawLine(395, $line_start, 395,  $this->y-$line_height);
                $page->drawLine(428, $line_start, 428,  $this->y-$line_height);
                $page->drawLine(478, $line_start, 478,  $this->y-$line_height);
                $page->drawLine(528, $line_start, 528,  $this->y-$line_height);                
                
            }
            $this->y -= $this->_ITEM_HEIGHT/2; 
            /*$line_start  = $this->y+30;
            $line_height = $this->_ITEM_HEIGHT+10;
            $page->setLineWidth(0.5);
            $page->drawLine(63,  $line_start, 63,   $this->y-$line_height);
            $page->drawLine(118, $line_start, 118,  $this->y-$line_height);
            $page->drawLine(343, $line_start, 343,  $this->y-$line_height);
            $page->drawLine(395, $line_start, 395,  $this->y-$line_height);
            $page->drawLine(428, $line_start, 428,  $this->y-$line_height);
            $page->drawLine(478, $line_start, 478,  $this->y-$line_height);
            $page->drawLine(528, $line_start, 528,  $this->y-$line_height);            
            */

          //rajoute les frais d'expédition
            if ($order->getStatus() != Ebulb_Purchase_Model_Order::STATUS_NEW )
            {
                $page->setLineWidth(1.5);
                $page->drawLine(10, $this->y+$this->_ITEM_HEIGHT-4, $this->_BLOC_ENTETE_LARGEUR,  $this->y+$this->_ITEM_HEIGHT-4);
                $style->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA_BOLD), 10);
                $this->DrawMultilineText($page, mage::helper('purchase')->__('Shipping costs'), 122, $this->y - 3, 10, 0.2, 11);
                $this->drawTextInBlock($page, number_format($order->getTaxRate(), 2).'%', 441, $this->y - 3, 40, 20, 'c'); 
                $this->drawTextInBlock($page, $order->getCurrency()->formatTxt($order->getShippingPrice()), 465, $this->y - 3, 60, 20, 'r');  
                $this->drawTextInBlock($page, $order->getCurrency()->formatTxt($order->getShippingPrice()), 520, $this->y - 3, 60, 20, 'r');
                             
                //rajoute les droits de douane
                $this->y -= $this->_ITEM_HEIGHT;
                $style->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA_BOLD), 10);
                $this->DrawMultilineText($page, mage::helper('purchase')->__('Toll costs'), 122, $this->y - 3, 10, 0.2, 11);
                $this->drawTextInBlock($page, number_format($order->getTaxRate(), 2).'%', 441, $this->y - 3, 40, 20, 'c');     
                $this->drawTextInBlock($page, $order->getCurrency()->formatTxt($order->getAdjustFee()), 465, $this->y - 3, 60, 20, 'r');  
                $this->drawTextInBlock($page, $order->getCurrency()->formatTxt($order->getAdjustFee()), 520, $this->y - 3, 60, 20, 'r');      
            
            }            
                  //entetes de colonnes
            
     }
      
	 public function drawTableHeader(&$page)
	 {
	 	
        //entetes de colonnes
        $page->setLineWidth(0.5);
        $this->y -= 15;
        $page->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA), 9);
        
        $header_height = 7;
        $header_start  = 15;
        
	 	$page->drawLine(0, 0, 0,  0);
        $page->drawText(mage::helper('purchase')->__(' Item NO'), 9, $this->y, 'UTF-8');
        
        $page->drawLine(63, $this->y+$header_start, 63,  $this->y-$header_height);
        $page->drawText(mage::helper('purchase')->__('Sku'), 65, $this->y, 'UTF-8');
        
        $page->drawLine(118, $this->y+$header_start, 118,  $this->y-$header_height);
        $page->drawText(mage::helper('purchase')->__('Description'), 120, $this->y, 'UTF-8');
        
        $page->drawLine(343, $this->y+$header_start, 343,  $this->y-$header_height);
        $page->drawText(mage::helper('purchase')->__('Unit Price'), 345, $this->y, 'UTF-8');
        
        $page->drawLine(395, $this->y+$header_start, 395,  $this->y-$header_height);
        $page->drawText(mage::helper('purchase')->__('Qty'), 400, $this->y, 'UTF-8');
        
        $page->drawLine(428, $this->y+$header_start, 428,  $this->y-$header_height);
        $page->drawText(mage::helper('purchase')->__('Tax'), 430, $this->y, 'UTF-8');
        
        $page->drawLine(478, $this->y+$header_start, 478,  $this->y-$header_height);
        $page->drawText(mage::helper('purchase')->__('Amount'), 480, $this->y, 'UTF-8');
        
        $page->drawLine(528, $this->y+$header_start, 528,  $this->y-$header_height);
        $page->drawText(mage::helper('purchase')->__('Total'), 530, $this->y, 'UTF-8');
                
        //barre grise fin entete colonnes
        $this->y -= 8;
        $page->setLineWidth(1.5);
        $page->drawLine(10, $this->y, $this->_BLOC_ENTETE_LARGEUR,  $this->y);
        
        $this->y -= 15;
        
	 }
	
}