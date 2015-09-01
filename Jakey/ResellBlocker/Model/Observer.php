<?php

class Jakey_ResellBlocker_Model_Observer{

	public function setValues(){


		$table = Mage::getModel('jakey_resellblocker/basetime');

		$timeFrame = Mage::getStoreConfig('jakeysell/general/totDays');

		$date = new Zend_Date(Mage::getModel('core/date')->timestamp());

		$collectionSize = Mage::getModel('jakey_resellblocker/basetime')
			->getCollection()
			->getSize();

		$collection = Mage::getModel('jakey_resellblocker/basetime')
			->getCollection();

		if($collectionSize<1){

			$table
				->setBaseTime($date);

			$table
				->setTimeFrame($timeFrame);

			$table
				->save();

		
		}elseif($timeFrame!==$collection->getFirstItem()->getTimeFrame()){

			foreach ($collection as $item) {

			    $item->delete();
			
			}

			$table
				->setBaseTime($date);

			$table
				->setTimeFrame($timeFrame);

			$table
				->save();


		}else{

			$first = $collection->getFirstItem();
			$b = $first->getBaseTime();

		}

	}

	public function checkTimeFrame(){

		$table = Mage::getModel('jakey_resellblocker/basetime');

		$timeFrame = Mage::getStoreConfig('jakeysell/general/totDays');

		$collection = $table->getCollection();

			$first = $collection->getFirstItem();

			$ba = $first->getBaseTime();

			$bDate= strtotime ( '+'.$timeFrame.' day' , strtotime ($ba));

			$future = date('m/d/Y',$bDate);

		$cdate = new Zend_Date(Mage::getModel('core/date')->timestamp());

		$timeDate = strtotime($cdate);

		$newDate = date("m/d/Y", $timeDate);


		if($newDate == $future){

			foreach ($collection as $item) {

			    $item->delete();
			
			}

			$table
				->setBaseTime($newDate);

			$table
				->setTimeFrame($timeFrame);

			$table
				->save();

		}


	}

	public function sendEmail(){

		$cdate = new Zend_Date(Mage::getModel('core/date')->timestamp());

		$cusServEmail = Mage::getStoreConfig('jakeysell/general/serviceEmail');

		$timeDate = strtotime($cdate);

		$newDate = date("m/d/Y", $timeDate);

		$emailTable = Mage::getModel('jakey_resellblocker/emaillist')
			->getCollection();

		foreach($emailTable as $potEmail){

			$emailAd = $potEmail->getEmailDate();
			$tEmailAd = strtotime($emailAd);
			$cEmailAd = date('m/d/Y', $tEmailAd);
			$orderNumber = $potEmail->getOrderNumber();

			if($cEmailAd == $newDate){

				$recipient = $potEmail->getCustomerEmail();

				$message = "Your order number ".$orderNumber." could not be fulfilled";

				$message = wordwrap($message, 70, "\r\n");

				mail($recipient, 'Order Canceled', $message);

				$servMess = "Order number ".$orderNumber." has been canceled. Customer has reached spend limit";

				$servMess = wordwrap($message, 70, "\r\n");

				$subject = "Resell Blocker - Order: ". $orderNumber;

				mail($$cusServEmail, $subject, $servMess);
			}

		}

		return;

	}

	public function evalSale($observer){


			Mage::log("SALE INFO:");

			$currentOrder = $observer->getEvent()->getOrder();
			$customer = $currentOrder->getCustomerId();
			$email = $currentOrder->getCustomerEmail();

			$capValue = Mage::getStoreConfig('jakeysell/general/spentCap');
			$timeFrame = Mage::getStoreConfig('jakeysell/general/totDays');
			$daysTillNote = Mage::getStoreConfig('jakeysell/general/noteDays');


			$collection = Mage::getModel('jakey_resellblocker/basetime')
							->getCollection();

			$first = $collection->getFirstItem();

			$ba = $first->getBaseTime();

			$bDate= strtotime ( '+'.$timeFrame.' day' , strtotime ($ba));

			$future = date('m/d/Y',$bDate);
	

			$collection = Mage::getModel('sales/order')->getCollection()
			->addAttributeToFilter('customer_id', $customer)
		    ->addAttributeToFilter('created_at', array('from'=>$ba));

			$totalGrandTotal = 0;

			foreach ($collection as $order) {
				
				$order_id = $order->getId();

				$order = Mage::getModel("sales/order")->load($order_id); //load order by order id 

				$total = $order->getGrandTotal();

				$totalGrandTotal += $total;
					

			}

			if($totalGrandTotal>$capValue){

				$order_id = $currentOrder->getIncrementId();
				$order_id = (string)$order_id;

				$currentOrder->cancel();
				$currentOrder->save();

				$currentDate = new Zend_Date(Mage::getModel('core/date')->timestamp());

				$eDate= strtotime ( '+'.$daysTillNote.' day' , strtotime ($currentDate));

				$emailTable = Mage::getModel('jakey_resellblocker/emaillist');


				$emailTable->setOrderNumber($order_id);
				$emailTable->setCustomerEmail($email);
				$emailTable->setEmailDate($eDate);

				$emailTable->save();



			}

		return;
	}
}
?>