<?php
class Demo_Observerexample_Model_Observer {
//Demo Example

 public function send_email($observer) {
		
	   $orderIds = $observer->getData('order_ids');

         foreach($orderIds as $_orderId){
           $order     = Mage::getModel('sales/order')->load($_orderId);
		   $store = Mage::getBaseUrl (Mage_Core_Model_Store::URL_TYPE_WEB); 
           //$customer  = Mage::getModel('customer/customer')->load($order->getData('customer_id'));
           //$customer->getDefaultBillingAddress()->getLastname();
           $billingaddress = $order->getBillingAddress();
		   $Ordercreated = $order->getCreatedAt();
           $OrderId = $order->getIncrementId();
		   $Getstaus = $order->getStatusLabel();
		   $shipping = $order->getShippingDescription(); // Shipping address for perticular product part.
		   $country = $billingaddress->getCountry();
		   $countryName = Mage::getModel('directory/country')->load($country)->getName();
		   
          try {  
                $params =    array( 'customerName'=>$order->getData('customer_firstname'),
		            'customerlastname'=>$order->getData('customer_lastname'),                           
                            'telephone'=>  $billingaddress->getData('telephone'),
                            'email'=> $billingaddress->getData('email'),
                            'street'=> $billingaddress->getData('street'),
                            'city'=>  $billingaddress->getData('city'),
                            'region'=> $billingaddress->getData('region'),
                            'postcode'=> $billingaddress->getData('postcode'),
                            'total'=>$order->getGrandTotal() );
				
                            
							//var_dump($Getstaus);
							//die();

							/* $url= 'http://localhost/allcat/getorders.php';         // the url on which the data will be send through curl
							$ch = curl_init();
							curl_setopt($ch, CURLOPT_URL, $url);
							curl_setopt($ch,CURLOPT_HEADER);
							curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
							curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
							curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
							$response = curl_exec($ch); 
							curl_close($ch);
							Mage::log('Order has been sent to exmaple.com'); */

	   						   $dbhost = '';
							   $dbuser = '';
							   $dbpass = '';
							   $conn = mysql_connect($dbhost, $dbuser, $dbpass);
							   
							   if(! $conn ) {
								  die('Could not connect: ' . mysql_error());
							   }

							  if($store) {
								   
							  $short_code = 'web10';	   
							   
							  $orderItemsCollection = $order->getItemsCollection();
							  foreach ($orderItemsCollection as $item){ 
							  
									  $productName = $item->getName();
									  $productQty = $item->getQtyOrdered();
									  $productPrice = $item->getPrice();
									  $payment_method = $order->getPayment()->getMethodInstance()->getTitle();

									
									   $sql = "INSERT INTO crm_order_management (email,product,comment,lname,qty,orderamt,pname,paymode,orderfro,despdate,orderid,status,storename,fname,short_code)
											   VALUES ('".$params['email']."','".$productName."','".$params['customerName']."','".$params['customerlastname']."','".$productQty."','".$params['total']."','".$params['telephone']."','".$payment_method."','".$params['city']."','".$Ordercreated."','".$OrderId."','".$Getstaus."','".$store."','".$countryName."','".$short_code."')";  
									   mysql_select_db('abortion_word');
									   $retval = mysql_query( $sql, $conn );
									   
								    if(!$retval) {
										  die('Could not enter data: ' . mysql_error());
								    }
							     }
							  }
							   
							   mysql_close($conn);
 
         } catch (Exception $e) {
               Mage::logException($e);}
      }
         return $this;
		
    }
}