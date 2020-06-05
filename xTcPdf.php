<?php
date_default_timezone_set('asia/kolkata');
App::uses('AppController', 'Controller');
App::uses('Folder', 'Utility');
App::uses('File', 'Utility');
App::uses('CakeEmail', 'Network/Email');
App::import('Vendor','xtcpdf');
// App::import('Vendor', 'dompdf',array('file'=>'dompdf_config.inc.php'));
// App::import('Vendor', 'dompdf', array('file' => 'dompdf' . DS . 'dompdf_config.inc.php'));
ini_set('memory_limit', '256M');
set_time_limit(0);
class HandlerController extends AppController
{
	public $helper=array('html', 'form', 'Js');

	public $components = array(
    'Paginator',
    'Session','Cookie','RequestHandler'
 	);
	
	
	//////////////////////////////////////////////////////////////--------------- Authentication  Start------------------------//////////////////////////////////////////////
	 public function beforeFilter() {
       Configure::write('debug',0);
    } 

	
	public function ticket_pdf(){
		// $this->layout='pdf';
		$id = $this->request->query('id');
		// print($id);die;
		$this->loadmodel('ticket_entry');
		$last_data=$this->ticket_entry->find('all', array('conditions' => array('id' => $id)));
		
		$tcpdf = new XTCPDF(); 
		$textfont = 'times'; // looks better, finer, and more condensed than 'dejavusans'

		//$tcpdf->SetAuthor("KBS Homes & Properties at http://kbs-properties.com"); 
		$tcpdf->SetAutoPageBreak( true ); 
		//$tcpdf->setHeaderFont(array($textfont,'',40)); 
		$tcpdf->xheadercolor = array(255,255,255); 
		$tcpdf->xheadertext = ''; 
		$tcpdf->xfootertext = '';

		// add a page (required with recent versions of tcpdf) 
		$tcpdf->AddPage(); 

		// Now you position and print your page content 
		// example:  
		$tcpdf->SetTextColor(0, 0, 0);
		$tcpdf->SetFont($textfont,12);
		$tcpdf->SetLineWidth(0.1);
		
		foreach(@$last_data as $row)
		{
		    $master_item_id=@explode(',',$row['ticket_entry']['master_item_id']);
		    $no_of_person=@explode(',',$row['ticket_entry']['no_of_person']);
		    $amount=@explode(',',$row['ticket_entry']['amount']);
		    $tot_amnt=$row['ticket_entry']['tot_amnt'];
		    $grand_amnt=$row['ticket_entry']['grand_amnt'];
		    $security_amnt=$row['ticket_entry']['security_amnt'];
		    $paid_amnt=$row['ticket_entry']['paid_amnt'];
		    $time=$row['ticket_entry']['time'];
		    $date=$row['ticket_entry']['date'];
		    $id=$row['ticket_entry']['id'];
		    $discount=$row['ticket_entry']['discount'];
		    $locker_no=$row['ticket_entry']['locker_no'];
		    $name_person=$row['ticket_entry']['name_person'];
		    $mobile=$row['ticket_entry']['mobile'];
		    $ticket_no=$row['ticket_entry']['ticket_no'];
		    $counter=$row['ticket_entry']['counter_id'];    
		}
		$name_person = strtoupper($name_person);
		$date=date('d-M-Y',strtotime($date));
		// echo $counter;die;
		$fetch_company_name=$this->requestAction(array('controller' => 'Handler', 'action' => 'fetch_company_name'), array());
		foreach($fetch_company_name as $company)
		{
		 $name=$company['company']['company_name'];
		 $address=$company['company']['address'];
		 $mobile_company=$company['company']['mobile'];
		}

		$format=<<<EOD
		<html xmlns="http://www.w3.org/1999/xhtml">
		<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>$name</title>
		<style>
		    body {
		        margin: 0 !important;
		        padding: 0 !important;
		    }
			
		    * {
		        box-sizing: border-box;
		        -moz-box-sizing: border-box;
				-webkit-box-sizing: border-box;
				border-collapse: collapse; 
		      }
			
			.page{
			width:100%;
			height:100%;
			margin:0 auto !important;
			}
			
			.left {
			float: left;
			width: 270px !important;;
			margin:0 auto;
			padding-left:5px;
			padding-top:5px;
			padding-bottom:10px;
			}
			
			.inner{
			font-family:Century Gothic;
			font-size:14px; 	
			text-transform:uppercase;
			}
			
		    @page {
		        size: A4;
		        margin: 0 !important;
		    }
			
		    @media print {
		        .page {
		            margin: 0;
		            border: 2px solid black;
		            border-radius: initial;
		            width: initial;
		            min-height: initial;
		            box-shadow: initial;
		            background: initial;
		        }
		    }
			#border{
		    display: none !important;
		}
		</style>
		</head>
		<body>		
		    <div style="border-top:2px solid black;border-left:2px solid black;border-right:2px solid black;">
		        <table width="100%" class="inner">
		        	<tr>
		        		<td colspan="4"></td>
		        	</tr>
		            <tr>
		                <td colspan="4" style="text-align:center;font-size:20px;"><b><span>$name</span></b></td>
		            </tr>
		            <tr>
		                <td colspan="4" style="text-align:center;line-height:15px;"><span>$address</span></td>
		            </tr>
		            <tr>
		                <td colspan="4" style="text-align:center;font-size:14px !important;"><span>MOBILE NO : $mobile_company</span></td>
		            </tr>
		            <tr>
		                <td colspan="4" style="text-align:center;font-size:20px;"><b><span>ENTRY TICKET</span></b></td>
		            </tr>
		            <tr>
		                <td colspan="4" align="center" style="font-size:25px"><b># $ticket_no</b></td>
		            </tr>
		            <tr>
		                <td colspan="4" align="center"><b>Date: $date</b></td>
		            </tr>
		            <tr>
		                <td colspan="4" align="center"><b>Counter:  Online</b></td>
		            </tr>
	            </table>
	        </div>
EOD;
$tcpdf->writeHTML($format, true, false, false, false, '');	            
		             if(!empty($name_person)) {
		            $format =<<<EOD
		        <div style="border-left:2px solid black;border-right:2px solid black;">
		            <table style="font-size:15px;margin-top:0;">
		            <tr>
		                <td colspan="2" align="right"><b>Name : &nbsp;&nbsp;</b></td>
                		<td colspan="2" align="left" ><b>$name_person</b></td>
		            </tr>
		            <tr>
		                <td colspan="2" align="right"><b>Mobile No. : &nbsp;&nbsp;</b></td>
                		<td colspan="2" align="left"><b>$mobile</b></td>
		            </tr>
		            </table>
		        </div>
EOD;
		        }
		        $tcpdf->writeHTML($format, true, false, false, false, '');
		        $format =<<<EOD
		       	<div style="border-left:2px solid black;border-right:2px solid black;">
			        <table width="90%">
			            <tr>
			                <td><b>CATEGORY</b></td>
			                <td><b>PRICE</b></td>
			                <td><b>NCS</b></td>
			                <td align="right"><b>TOTAL</b></td>
	            		</tr>
	            	</table>
	            </div>
EOD;
		        $tcpdf->writeHTML($format, true, false, false, false, '');
		            
		            for($i=0;$i<sizeof($master_item_id);$i++)
		            {
		            $category=$this->requestAction(array('controller' => 'Handler', 'action' => 'fetchmasteritemname',$master_item_id[$i]), array());
		            $plan_name=$this->requestAction(array('controller' => 'Handler', 'action' =>'fetchmasterrate',$master_item_id[$i]), array());
		                $format =<<<EOD
		                <div style="border-left:2px solid black;border-right:2px solid black;">
			                <table width="90%">
			                <tr>
				                <td>$category</td>
				                <td>$plan_name</td>
				                <td>$no_of_person[$i]</td>
				                <td align="right">$amount[$i]</td>
				            </tr>
				            </table>
			            </div>
EOD;
				$tcpdf->writeHTML($format, true, false, false, false, '');
				}
		        
		         
// 		            // echo $content_for_layout;die;,
		            $format =<<<EOD
		            <div style="border-left:2px solid black;border-right:2px solid black;">
			            <table>
			            <tr>
			                <td colspan="2" style="font-style:italic;font-size:12px;">Inclusive of all taxes</td>
			                <td style="text-align:right;"><b>TOTAL:</b></td>
			                <td align="center">$tot_amnt</td>
			            </tr>
			            </table>
		            </div>
EOD;
				$tcpdf->writeHTML($format, true, false, false, false, '');
		            
// 		            if($discount>0) {
// 		            $format =<<<EOD
// 		            <table>
// 		            <tr>
// 		                <td align="right"colspan="3" style="text-align:right;"><b>Discount:</b></td>
//                 		<td align="right">$discount</td>
// 		            </tr>
// 		            </table>
// EOD; 
// 				$tcpdf->writeHTML($format, true, false, false, false, '');
// 				}	
				
		            $format =<<<EOD
		            <div style="border-left:2px solid black;border-right:2px solid black;">
			            <table>
			            <tr>
			                <td colspan="3" style="text-align:right;"><b>GRAND TOTAL:</b></td>
	               			<td align="center">$grand_amnt</td>
			            </tr>
			            </table>
		            </div>
EOD;
				$tcpdf->writeHTML($format, true, false, false, false, '');
		            
		            if($security_amnt>0){
		            $format =<<<EOD
		            <div style="border-left:2px solid black;border-right:2px solid black;">
			            <table>
			            <tr>
			                <td colspan="3" style="text-align:right;"><b>RUFUNDABLE AMOUNT:</b></td>
			                <td align="center"><$security_amnt</td>
			            </tr>
			            </table>
		            </div>
EOD;
				$tcpdf->writeHTML($format, true, false, false, false, '');
		            
		            }

		            $format =<<<EOD
		            <div style="border-bottom:2px solid black;border-left:2px solid black;border-right:2px solid black;">
			            <table>
			            <tr>
			            	<td colspan="4" style="height:10px;border-top:2px dotted black;width:99%"></td>
			            </tr>
			            <tr>
			                <td colspan="3" style="text-align:right;padding-top:5px;"><b>AMOUNT TO BE PAID:</b></td>
	                		<td align="center" style="width:26%;"><strong>$paid_amnt</strong></td>
			            </tr>
			        </table>
			    </div>
EOD;
		$tcpdf->writeHTML($format, true, false, false, false, '');
		ob_end_clean();
		echo $tcpdf->Output('Invoice.pdf', 'F'); 
		// die;
		// print_r($hell);die;
		$this->redirect(array('action' => 'payment_gateway',$id));
	}
	//////////////////////  End Php Function  //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

}
?>
