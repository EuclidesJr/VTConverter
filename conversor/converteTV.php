<?php
	session_start();
	/*
	 * 	Copyright (c) 2015 Euclides Cardoso Júnior <euclides.c.jr@hotmail.com>
	 * 	Permission is hereby granted, free of charge, to any person
	 * 	obtaining a copy of this software and associated documentation
	 * 	files (the "Software"), to deal in the Software without
	 * 	restriction, including without limitation the rights to use, copy,
	 * 	modify, merge, publish, distribute, sublicense, and/or sell copies
	 * 	of the Software, and to permit persons to whom the Software is
	 * 	furnished to do so, subject to the following conditions:
	 * 
	 * 	The above copyright notice and this permission notice shall be
	 * 	included in all copies or substantial portions of the Software.
	 * 
	 * 	THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
	 * 	EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
	 * 	OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
	 * 	NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
	 * 	HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
	 * 	WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
	 * 	FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR
	 * 	OTHER DEALINGS IN THE SOFTWARE.
	 *  
	 * */
	require("../dbconfig.php");	
	$arq = '../upload/'.$_REQUEST['arq'];	
	//Cria o novo documento
	$novoXML = new DOMDocument( "1.0", "ISO-8859-15" );
	$novoXML->formatOutput = true;	
	/* Variaveis globais para os elementos*/
	$novoXML_description 		   	= NULL;
	$novoXML_virtualInfrastructure 	= NULL;
	$novoXML_vNode				   	= NULL;
	$novoXML_vStorage			   	= NULL;
	$novoXML_vLink					= NULL;
	$novoXML_vRouter				= NULL;
	$novoXML_vAccessPoint			= NULL;
	$novoXML_tag					= NULL;
	$novoXML_capacity				= NULL;
	$novoXML_storage				= NULL;
	$novoXML_cpu					= NULL;
	$novoXML_interval				= NULL;
	$novoXML_scale					= NULL;
	$novoXML_loadBalancer			= NULL;
	$novoXML_nat					= NULL;
	$novoXML_masquerade				= NULL;
	$novoXML_controlPlane			= NULL;
	$novoXML_routingTable			= NULL;
	$novoXML_route					= NULL;
	try{		
		function constroi_description(){
			global $novoXML, $novoXML_description;
			$novoXML_description = $novoXML->createElement( "description" );
			$novoXML_description->setAttribute( "xmlns","http://www.vxdlforum.org/vxdl");
			$novoXML_description->setAttribute( "xmlns:xsi","http://www.w3.org/2001/XMLSchema-instance");
			$novoXML_description->setAttribute( "xsi:schemaLocation","http://www.vxdlforum.org/vxdl VXDL.xsd");
			$novoXML->appendChild( $novoXML_description );			
		}		
		function constroi_virtualInfrastructure($st){
			global $novoXML, $novoXML_virtualInfrastructure,$novoXML_description;
			$novoXML_virtualInfrastructure = $novoXML->createElement("virtualInfrastructure");
			if($st->hasAttribute ( "id" )){
				$novoXML_virtualInfrastructure->appendChild($novoXML->createElement( "id", $st->getAttribute( "id" )));
			} 
			if ($st->hasChildNodes()) {
		    	$st_filhos = $st->childNodes;
		        foreach($st_filhos as $i) {					
					switch ($i->nodeName) {
						case 'Tags':
							constroi_virtualInfrastructure_propriedades($i);
							break;
						case 'TopologyTemplate':
							constroi_topologia($i);
							break;	
						case '#text':
							break;
						default:
							throw new RuntimeException('Impossivel converter arquivo. O elemento '.$i->nodeName.' não existe na linguagem.');
							break;					
					}
				}
			}
			$novoXML_description->appendChild( $novoXML_virtualInfrastructure);
		}
		function constroi_virtualInfrastructure_propriedades($vi){
			global $novoXML, $novoXML_virtualInfrastructure;
			if ($vi->hasChildNodes()) {
		    	$vi_filhos = $vi->childNodes;
		        foreach($vi_filhos as $i) {		        	
					switch ($i->nodeName) {
						case 'Tag':
								$p = $i->getAttribute('name'); 
								$v = $i->getAttribute('value');
								$novoXML_virtualInfrastructure->appendChild($novoXML->createElement( $p, $v ));
							break;	
						case '#text':
							break;
						default:
							throw new RuntimeException('Impossivel converter arquivo. O elemento '.$i->nodeName.' não existe na linguagem.');
							break;		
					}
				}
			}
		}
		function constroi_topologia($tp){
			global $novoXML, $novoXML_virtualInfrastructure,$novoXML_vNode, $novoXML_vStorage,$novoXML_vLink,$novoXML_vRouter,$novoXML_vAccessPoint;
			if ($tp->hasChildNodes()) {
		    	$tp_filhos = $tp->childNodes;
		        foreach($tp_filhos as $i) {		        	
					switch ($i->nodeName) {
						case 'NodeTemplate':
							switch ($i->getAttribute("type")) {
								case 'TOSCAvNodeType':
									$novoXML_vNode = $novoXML->createElement( "vNode" );
									if($i->hasAttribute ( "id" )){
										$novoXML_vNode->appendChild($novoXML->createElement( "id", $i->getAttribute( "id" )));
									} 
									constroi_vNode($i);
									$novoXML_virtualInfrastructure->appendChild( $novoXML_vNode );
									break;
								case 'TOSCAvStorageType':
									$novoXML_vStorage = $novoXML->createElement( "vStorage" );
									if($i->hasAttribute ( "id" )){
										$novoXML_vStorage->appendChild($novoXML->createElement( "id", $i->getAttribute( "id" )));
									}
									constroi_vStorage($i);
									$novoXML_virtualInfrastructure->appendChild( $novoXML_vStorage );
									break;
								case 'TOSCAvRouterType':
									$novoXML_vRouter = $novoXML->createElement( "vRouter" );
									if($i->hasAttribute ( "id" )){
										$novoXML_vRouter->appendChild($novoXML->createElement( "id", $i->getAttribute( "id" )));
									}
									constroi_vRouter($i);
									$novoXML_virtualInfrastructure->appendChild( $novoXML_vRouter );
									break;
								case 'TOSCAvAccessPointType':
									$novoXML_vAccessPoint = $novoXML->createElement( "vAccessPoint" );
									if($i->hasAttribute ( "id" )){
										$novoXML_vAccessPoint->appendChild($novoXML->createElement( "id", $i->getAttribute( "id" )));
									}
									constroi_vAccessPoint($i);
									$novoXML_virtualInfrastructure->appendChild( $novoXML_vAccessPoint );
									break;
								case '#text':
									break;
								default:
									throw new RuntimeException('Impossivel converter arquivo. O elemento '.$i->nodeName.' não existe na linguagem.');
									break;
							}
							break;
						case 'RelationshipTemplate':
							$novoXML_vLink = $novoXML->createElement( "vLink" );
							if($i->hasAttribute ( "id" )){
								$novoXML_vLink->appendChild($novoXML->createElement( "id", $i->getAttribute( "id" )));
							} 
							constroi_vLink($i);
							$novoXML_virtualInfrastructure->appendChild( $novoXML_vLink );
							break;
						case '#text':
							break;
						default:
							throw new RuntimeException('Impossivel converter arquivo. O elemento '.$i->nodeName.' não existe na linguagem.');
							break;
					}
				}
			}	
		}
		function constroi_tag($tag){
			global $novoXML_tag,$novoXML;			
			if ($tag->hasChildNodes()) {
		    	$tag_filhos = $tag->childNodes;
		        foreach($tag_filhos as $i) {
					switch ($i->nodeName) {
						case 'key':
							$novoXML_tag->appendChild( $novoXML->createElement( "key", $i->nodeValue ) );
							break;
						case 'value':
							$novoXML_tag->appendChild($novoXML->createElement( "value", $i->nodeValue ));
							break;
						default:							
							break;
					}
				}
			}
		}
		function constroi_vLink($rt){
			global$novoXML,$novoXML_vLink,$novoXML_bandwidth,$novoXML_capacity,$novoXML_tag;
			if ($rt->hasChildNodes()) {
			   	$rt_filhos = $rt->childNodes;
		        foreach($rt_filhos as $properties) {
		        	$vLinkProperties = $properties->childNodes;		        	
					foreach($vLinkProperties as $vLP) {
						$elementos = $vLP->childNodes;
						foreach ($elementos as $i) {
							switch ($i->nodeName) {
								case 'location':
									$novoXML_vLink->appendChild($novoXML->createElement( "location", $i->nodeValue ));
									break;
								case 'startDate':
									$novoXML_vLink->appendChild($novoXML->createElement( "startDate", $i->nodeValue ));
									break;
								case 'totalTime':
									$novoXML_vLink->appendChild($novoXML->createElement( "totalTime", $i->nodeValue ));
									break;
								case 'model':
									$novoXML_vLink->appendChild($novoXML->createElement( "model", $i->nodeValue ));
									break;
								case 'exclusivity':
									$novoXML_vLink->appendChild($novoXML->createElement( "exclusivity", $i->nodeValue ));
									break;
								case 'tag':
									$novoXML_tag = $novoXML->createElement( "tag" );
									constroi_tag($i);
									$novoXML_vLink->appendChild($novoXML_tag);
									break;
								case 'source':
									//Valido se o elemento source é um NodeTemplate
									//if (array_key_exists ( $i->nodeValue ,$arrNT ) && isset($arrNT[$i->nodeValue])) {
										$SourceElement = $novoXML->createElement( "SourceElement");
										$SourceElement->setAttribute( "ref", $i->nodeValue );
										$novoXML_vLink->appendChild( $SourceElement );								
									//}else{
									//	throw new RuntimeException('Elemento '.$i->nodeValue.' não pode ser utlizado como source no RelationshipTemplate');
									//}
									break;
								case 'destination':
									//Valido se o elemento destination é um NodeTemplate
									//if (array_key_exists ( $i->nodeValue ,$arrNT ) && isset($arrNT[$i->nodeValue])) {
										$TargetElement = $novoXML->createElement( "TargetElement");
										$TargetElement->setAttribute( "ref", $i->nodeValue );
										$novoXML_vLink->appendChild( $TargetElement );
									//}else{
									//	throw new RuntimeException('Elemento '.$i->nodeValue.' não pode ser utlizado como destination no RelationshipTemplate');
									//}
									break;
								case 'bandwidth':
									$novoXML_bandwidth = $novoXML->createElement( "bandwidth" );
									constroi_bandwidth($i);
									$novoXML_vLink->appendChild($novoXML_bandwidth);
									break;
								case 'latency':
									$novoXML_capacity = $novoXML->createElement( "latency" );
									constroi_Capacity($i);
									$novoXML_vLink->appendChild($novoXML_capacity);
									break;
								case '#text':
									break;
								default:
									throw new RuntimeException('Impossivel converter arquivo. O elemento '.$i->nodeName.' não existe na linguagem.');
									break;
							}
						}
					}
				}
			}
		}
		function constroi_vNode($nt){
			global $novoXML, $novoXML_virtualInfrastructure,$novoXML_vNode,$novoXML_tag,$novoXML_capacity,$novoXML_storage,$novoXML_cpu;
			if ($nt->hasChildNodes()) {
				/*$properties = $nt->getElementsByTagName("Properties");
				foreach ($properties as $p) {
					echo $p->nodeName." - ".$p->nodeValue."-----<br>";
				}
				$properties_filhos = $properties->getElementsByTagName("my:vNodeProperties");
				foreach ($properties_filhos as $i) //go to each section 1 by 1
				{
					echo "----- ".$i->nodeValue;	
				}*/
				
		    	$nt_filhos = $nt->childNodes;
		        foreach($nt_filhos as $properties) {
		        	//echo "<bt>---".$properties->nodeName."---<br>";
					$vNodeProperties = $properties->childNodes;		        	
					foreach($vNodeProperties as $vNP) {
						//echo "<br>***--------_________-".$vNP->nodeName."***<br>";
						$elementos = $vNP->childNodes;
						//constroi_Comum($elementos, $novoXML_vNode);
						foreach ($elementos as $i) {
							switch ($i->nodeName) {
								case 'tag':
									$novoXML_tag = $novoXML->createElement( "tag" );
									constroi_tag($i);
									$novoXML_vNode->appendChild($novoXML_tag);
									break;
								case 'location':
									$novoXML_vNode->appendChild($novoXML->createElement( "location", $i->nodeValue ));
									break;
								case 'startDate':
									$novoXML_vNode->appendChild($novoXML->createElement( "startDate", $i->nodeValue ));
									break;
								case 'totalTime':
									$novoXML_vNode->appendChild($novoXML->createElement( "totalTime", $i->nodeValue ));
									break;
								case 'model':
									$novoXML_vNode->appendChild($novoXML->createElement( "model", $i->nodeValue ));
									break;
								case 'exclusivity':
									$novoXML_vNode->appendChild($novoXML->createElement( "exclusivity", $i->nodeValue ));
									break;								
								case 'device':
									$novoXML_vNode->appendChild($novoXML->createElement( "device", $i->nodeValue ));
									break;
								case 'image':
									$novoXML_vNode->appendChild($novoXML->createElement( "image", $i->nodeValue ));
									break;
								case 'region':
									$novoXML_vNode->appendChild($novoXML->createElement( "region", $i->nodeValue ));
									break;
								case 'memory':
									$novoXML_capacity = $novoXML->createElement( "memory" );
									constroi_Capacity($i);
									$novoXML_vNode->appendChild($novoXML_capacity);
									break;				
								case 'storage':
									$novoXML_capacity = $novoXML->createElement( "storage" );
									constroi_Capacity($i);
									$novoXML_vNode->appendChild($novoXML_capacity);
									break;
								case 'cpu':
									$novoXML_cpu = $novoXML->createElement( "cpu" );
									constroi_CPU($i);
									$novoXML_vNode->appendChild($novoXML_cpu);
									break;
								case '#text':
									break;
								default:
									throw new RuntimeException('Impossivel converter arquivo. O elemento '.$i->nodeName.' não existe na linguagem.');
									break;
							}
							//echo "<br>***&&&".$i->nodeName." - ".$i->nodeValue."***<br>";	
							//$novoXML_vNode->appendChild($novoXML->createElement($i->nodeName, $i->nodeValue));
						}
					}
					//$novoXML_virtualInfrastructure->appendChild($novoXML_vNode);										
				}
			}			
		}
		function constroi_vStorage($st){
			global $novoXML,$novoXML_vStorage,$novoXML_capacity,$novoXML_tag;
			if ($st->hasChildNodes()) {
			   	$st_filhos = $st->childNodes;
		        foreach($st_filhos as $properties) {
		        	$vStorageProperties = $properties->childNodes;		        	
					foreach($vStorageProperties as $vSP) {
						$elementos = $vSP->childNodes;
						foreach ($elementos as $i) {
							switch ($i->nodeName) {
								case 'location':
									$novoXML_vStorage->appendChild($novoXML->createElement( "location", $i->nodeValue ));
									break;
								case 'startDate':
									$novoXML_vStorage->appendChild($novoXML->createElement( "startDate", $i->nodeValue ));
									break;
								case 'totalTime':
									$novoXML_vStorage->appendChild($novoXML->createElement( "totalTime", $i->nodeValue ));
									break;
								case 'model':
									$novoXML_vStorage->appendChild($novoXML->createElement( "model", $i->nodeValue ));
									break;
								case 'exclusivity':
									$novoXML_vStorage->appendChild($novoXML->createElement( "exclusivity", $i->nodeValue ));
									break;
								case 'tag':
									$novoXML_tag = $novoXML->createElement( "tag" );
									constroi_tag($i);
									$novoXML_vStorage->appendChild($novoXML_tag);
									break;
								case 'type':
									$novoXML_vStorage->appendChild($novoXML->createElement( "type", $i->nodeValue ));
									break;
								case 'image':
									$novoXML_vStorage->appendChild($novoXML->createElement( "image", $i->nodeValue ));
									break;
								case 'region':
									$novoXML_vStorage->appendChild($novoXML->createElement( "region", $i->nodeValue ));
									break;
								case 'size':
									$novoXML_capacity = $novoXML->createElement( "size" );
									constroi_Capacity($i);
									$novoXML_vStorage->appendChild($novoXML_capacity);
									break;
								case '#text':
									break;
								default:
									throw new RuntimeException('Impossivel converter arquivo. O elemento '.$i->nodeName.' não existe na linguagem.');
									break;
							}
						}
					}
				}
			}						
		}
		function constroi_vRouter($rt){
			global $novoXML,$novoXML_vRouter,$novoXML_capacity,$novoXML_controlPlane,$novoXML_tag;
			if ($rt->hasChildNodes()) {
			   	$rt_filhos = $rt->childNodes;
		        foreach($rt_filhos as $properties) {
		        	$vRouterProperties = $properties->childNodes;		        	
					foreach($vRouterProperties as $vRP) {
						$elementos = $vRP->childNodes;
						foreach ($elementos as $i) {
							switch ($i->nodeName) {
								case 'location':
									$novoXML_vRouter->appendChild($novoXML->createElement( "location", $i->nodeValue ));
									break;
								case 'startDate':
									$novoXML_vRouter->appendChild($novoXML->createElement( "startDate", $i->nodeValue ));
									break;
								case 'totalTime':
									$novoXML_vRouter->appendChild($novoXML->createElement( "totalTime", $i->nodeValue ));
									break;
								case 'model':
									$novoXML_vRouter->appendChild($novoXML->createElement( "model", $i->nodeValue ));
									break;
								case 'exclusivity':
									$novoXML_vRouter->appendChild($novoXML->createElement( "exclusivity", $i->nodeValue ));
									break;
								case 'tag':
									$novoXML_tag = $novoXML->createElement( "tag" );
									constroi_tag($i);
									$novoXML_vRouter->appendChild($novoXML_tag);
									break;
								case 'region':
									$novoXML_vRouter->appendChild($novoXML->createElement( "region", $i->nodeValue ));
									break;
								case 'memory':
									$novoXML_capacity = $novoXML->createElement( "memory" );
									constroi_Capacity($i);
									$novoXML_vRouter->appendChild($novoXML_capacity);
									break;
								case 'controlPlane':
									$novoXML_controlPlane = $novoXML->createElement( "controlPlane" );
									constroi_controlPlane($i);
									$novoXML_vRouter->appendChild($novoXML_controlPlane);
									break;
								case '#text':
									break;
								default:
									throw new RuntimeException('Impossivel converter arquivo. O elemento '.$i->nodeName.' não existe na linguagem.');
									break;
							}
						}
					}
				}
			}
		}
		function constroi_vAccessPoint($at){
			global $novoXML,$novoXML_vAccessPoint,$novoXML_loadBalancer,$novoXML_nat,$novoXML_masquerade,$novoXML_tag;
			if ($at->hasChildNodes()) {
			   	$at_filhos = $at->childNodes;
		        foreach($at_filhos as $properties) {
		        	$vAccessPointProperties = $properties->childNodes;		        	
					foreach($vAccessPointProperties as $vAP) {
						$elementos = $vAP->childNodes;
						foreach ($elementos as $i) {
							switch ($i->nodeName) {
								case 'location':
									$novoXML_vAccessPoint->appendChild($novoXML->createElement( "location", $i->nodeValue ));
									break;
								case 'startDate':
									$novoXML_vAccessPoint->appendChild($novoXML->createElement( "startDate", $i->nodeValue ));
									break;
								case 'totalTime':
									$novoXML_vAccessPoint->appendChild($novoXML->createElement( "totalTime", $i->nodeValue ));
									break;
								case 'model':
									$novoXML_vAccessPoint->appendChild($novoXML->createElement( "model", $i->nodeValue ));
									break;
								case 'exclusivity':
									$novoXML_vAccessPoint->appendChild($novoXML->createElement( "exclusivity", $i->nodeValue ));
									break;
								case 'tag':
									$novoXML_tag = $novoXML->createElement( "tag" );
									constroi_tag($i);
									$novoXML_vAccessPoint->appendChild($novoXML_tag);
									break;
								case 'region':
									$novoXML_vAccessPoint->appendChild($novoXML->createElement( "region", $i->nodeValue ));
									break;
								case 'loadBalancer':
									$novoXML_loadBalancer = $novoXML->createElement( "loadBalancer" );
									constroi_loadBalancer($i);
									$novoXML_vAccessPoint->appendChild($novoXML_loadBalancer);
									break;
								case 'nat':
									$novoXML_nat = $novoXML->createElement( "nat" );
									constroi_nat($i);
									$novoXML_vAccessPoint->appendChild($novoXML_nat);
									break;
								case 'masquerade':
									$novoXML_masquerade = $novoXML->createElement( "masquerade" );
									constroi_masquerade($i);
									$novoXML_vAccessPoint->appendChild($novoXML_masquerade);
									break;
								case '#text':
									break;
								default:
									throw new RuntimeException('Impossivel converter arquivo. O elemento '.$i->nodeName.' não existe na linguagem.');
									break;
							}
						}
					}
				}
			}
		} 
		function constroi_bandwidth($bandwidth){
			global $novoXML, $novoXML_capacity, $novoXML_bandwidth;		
			if ($bandwidth->hasChildNodes()) {
		    	$bandwidth_filhos = $bandwidth->childNodes;
		        foreach($bandwidth_filhos as $i) {
					switch ($i->nodeName) {
						case 'forward':
							$novoXML_capacity = $novoXML->createElement( "forward" );
							constroi_Capacity($i);
							$novoXML_bandwidth->appendChild($novoXML_capacity);
							break;
						case 'reverse':
							$novoXML_capacity = $novoXML->createElement( "reverse" );
							constroi_Capacity($i);
							$novoXML_bandwidth->appendChild($novoXML_capacity);
							break;
						case '#text':
							break;
						default:
							throw new RuntimeException('Impossivel converter arquivo. O elemento '.$i->nodeName.' não existe na linguagem.');
							break;					}
				}
			}
		}
		function constroi_CPU($cpu){
			global $novoXML_cpu,$novoXML,$novoXML_capacity;
			if ($cpu->hasChildNodes()) {
		    	$cpu_filhos = $cpu->childNodes;
		        foreach($cpu_filhos as $i) {
		        	switch ($i->nodeName) {
						case 'architecture':
							$novoXML_cpu = $novoXML->createElement( "architecture", $i->nodeValue );
							break;
						case 'cores':
							$novoXML_capacity = $novoXML->createElement( "cores" );
							constroi_Capacity($i);
							$novoXML_cpu->appendChild($novoXML_capacity);
							break;
						case 'frequency':
							$novoXML_capacity = $novoXML->createElement( "frequency" );
							constroi_Capacity($i);
							$novoXML_cpu->appendChild($novoXML_capacity);
							break;
						case '#text':
							break;
						default:
							throw new RuntimeException('Impossivel converter arquivo. O elemento '.$i->nodeName.' não existe na linguagem.');
							break;
					}
				}
			}
		}
		function constroi_Capacity($capacity){
			global $novoXML_capacity,$novoXML, $novoXML_interval,$novoXML_scale;
			if ($capacity->hasChildNodes()) {
		    	$capacity_filhos = $capacity->childNodes;
		        foreach($capacity_filhos as $i) {
					switch ($i->nodeName) {
						case 'unit':
							$novoXML_capacity->appendChild( $novoXML->createElement( "unit", $i->nodeValue ));
							break;
						case 'event':
							$novoXML_capacity->appendChild($novoXML->createElement( "event", $i->nodeValue ));
							break;
						case 'interval':
							$novoXML_interval = $novoXML->createElement( "interval" );
							constroi_Interval($i);
							$novoXML_capacity->appendChild($novoXML_interval);
							break;
						case 'simple':
							$novoXML_capacity->appendChild( $novoXML->createElement( "simple", $i->nodeValue ));
							break;
						case 'set':
							$novoXML_capacity->appendChild($novoXML->createElement( "set", $i->nodeValue ));
							break;
						case 'scale':
							$novoXML_scale = $novoXML->createElement( "scale" );
							constroi_Scale($i);
							$novoXML_capacity->appendChild($novoXML_scale);
							break;
						case '#text':
							break;
						default:
							throw new RuntimeException('Impossivel converter arquivo. O elemento '.$i->nodeName.' não existe na linguagem.');
							break;
					 }   	
		        }
			}	    
		}
		function constroi_Interval($interval){
			global $novoXML, $novoXML_interval;		
			if ($interval->hasChildNodes()) {
		    	$interval_filhos = $interval->childNodes;
		        foreach($interval_filhos as $i) {
					switch ($i->nodeName) {
						case 'min':
							$novoXML_interval->appendChild( $novoXML->createElement( "min", $i->nodeValue ));
							break;
						case 'max':
							$novoXML_interval->appendChild( $novoXML->createElement( "max", $i->nodeValue ));
							break;
						case '#text':
							break;
						default:
							throw new RuntimeException('Impossivel converter arquivo. O elemento '.$i->nodeName.' não existe na linguagem.'); 
							break;
					}
				}
			}
		}
		function constroi_Scale($scale){
			global $novoXML, $novoXML_scale;		
			if ($scale->hasChildNodes()) {
		    	$scale_filhos = $scale->childNodes;
		        foreach($scale_filhos as $i) {
					switch ($i->nodeName) {
						case 'min':
							$novoXML_scale->appendChild( $novoXML->createElement( "min", $i->nodeValue ));
							break;
						case 'max':
							$novoXML_scale->appendChild( $novoXML->createElement( "max", $i->nodeValue ));
							break;
						case 'step':
							$novoXML_scale->appendChild( $novoXML->createElement( "step", $i->nodeValue ));
							break;
						case '#text':
							break;
						default:
							throw new RuntimeException('Impossivel converter arquivo. O elemento '.$i->nodeName.' não existe na linguagem.');
							break;
					}
				}
			}
		}
		function constroi_loadBalancer($loadBalancer){
			global $novoXML, $novoXML_loadBalancer;
			if ($loadBalancer->hasChildNodes()) {
		    	$loadBalancer_filhos = $loadBalancer->childNodes;
		        foreach($loadBalancer_filhos as $i) {
					switch ($i->nodeName) {
						case 'inEndpoint':
							$novoXML_loadBalancer->appendChild( $novoXML->createElement( "inEndpoint", $i->nodeValue ));
							break;
						case 'region':
							$novoXML_loadBalancer->appendChild( $novoXML->createElement( "region", $i->nodeValue ));
							break;
						case 'addressNumber':
							$novoXML_loadBalancer->appendChild( $novoXML->createElement( "addressNumber", $i->nodeValue ));
							break;
						case '#text':
							break;
						default:
							throw new RuntimeException('Impossivel converter arquivo. O elemento '.$i->nodeName.' não existe na linguagem.');
							break;
					}
				}
			}
		}
		function constroi_nat($nat){
			global $novoXML, $novoXML_nat;
			if ($nat->hasChildNodes()) {
		    	$nat_filhos = $nat->childNodes;
		        foreach($nat_filhos as $i) {
					switch ($i->nodeName) {
						case 'inEndpoint':
							$novoXML_nat->appendChild( $novoXML->createElement( "inEndpoint", $i->nodeValue ));
							break;
						case 'inPort':
							//$novoXML_NTP->appendChild( $novoXML->createElement( "device", $i->nodeValue ));
							$novoXML_nat->appendChild( $novoXML->createElement( "inPort", $i->nodeValue ));
							break;
						case 'protocol':
							$novoXML_nat->appendChild( $novoXML->createElement( "protocol", $i->nodeValue ));
							break;
						case 'region':
							$novoXML_nat->appendChild( $novoXML->createElement( "region", $i->nodeValue ));
							break;
						case 'outPort':
							$novoXML_nat->appendChild( $novoXML->createElement( "outPort", $i->nodeValue ));
							break;
						case '#text':
							break;
						default:
							throw new RuntimeException('Impossivel converter arquivo. O elemento '.$i->nodeName.' não existe na linguagem.');				 
							break;
					}
				}
			}
		}
		function constroi_masquerade($masquerade){
			global $novoXML, $novoXML_masquerade;
			if ($masquerade->hasChildNodes()) {
		    	$masquerade_filhos = $masquerade->childNodes;
		        foreach($masquerade_filhos as $i) {
					switch ($i->nodeName) {
						case 'inEndpoint':
							$novoXML_masquerade->appendChild( $novoXML->createElement( "inEndpoint", $i->nodeValue ));
							break;
						case 'region':
							$novoXML_masquerade->appendChild( $novoXML->createElement( "region", $i->nodeValue ));
							break;
						case '#text':
							break;
						default:
							throw new RuntimeException('Impossivel converter arquivo. O elemento '.$i->nodeName.' não existe na linguagem.'); 
							break;
					}
				}
			}
		}
		function constroi_controlPlane($controlPlane){
			global $novoXML, $novoXML_controlPlane, $novoXML_routingTable;
			if ($controlPlane->hasChildNodes()) {
		    	$controlPlane_filhos = $controlPlane->childNodes;
		        foreach($controlPlane_filhos as $i) {
					switch ($i->nodeName) {
						case 'layer':
							$novoXML_controlPlane->appendChild( $novoXML->createElement( "layer", $i->nodeValue ));
							break;
						case 'type':
							$novoXML_controlPlane->appendChild( $novoXML->createElement( "type", $i->nodeValue ));
							break;
						case 'routingProtocol':
							$novoXML_controlPlane->appendChild( $novoXML->createElement( "routingProtocol", $i->nodeValue ));
							break;
						case 'routingTable':
							$novoXML_routingTable = $novoXML->createElement( "routingTable" );
							constroi_routingTable($i);
							$novoXML_controlPlane->appendChild( $novoXML_routingTable );
							break;
						case '#text':
							break;
						default:
							throw new RuntimeException('Impossivel converter arquivo. O elemento '.$i->nodeName.' não existe na linguagem.'); 
							break;
					}
				}
			}
		}
		function constroi_routingTable($routingTable){
			global $novoXML, $novoXML_routingTable, $novoXML_route;
			if ($routingTable->hasChildNodes()) {
		    	$routingTable_filhos = $routingTable->childNodes;
		        foreach($routingTable_filhos as $i) {
					switch ($i->nodeName) {
						case 'rtSize':
							$novoXML_routingTable->appendChild( $novoXML->createElement( "rtSize", $i->nodeValue ));
							break;
						case 'route':
							$novoXML_route = $novoXML->createElement( "route" );
							constroi_route($i);
							$novoXML_routingTable->appendChild($novoXML_route);
							break;
						case '#text':
							break;
						default:
							throw new RuntimeException('Impossivel converter arquivo. O elemento '.$i->nodeName.' não existe na linguagem.'); 
							break;
					}
				}
			}
		}
		function constroi_route($route){
			global $novoXML, $novoXML_route;
			if ($route->hasChildNodes()) {
		    	$route_filhos = $route->childNodes;
		        foreach($route_filhos as $i) {
					switch ($i->nodeName) {
						case 'source':
							$novoXML_route->appendChild( $novoXML->createElement( "source", $i->nodeValue ));
							break;
						case 'destination':
							$novoXML_route->appendChild( $novoXML->createElement( "destination", $i->nodeValue ));
							break;
						case 'vLinkIn':
							$novoXML_route->appendChild( $novoXML->createElement( "vLinkIn", $i->nodeValue ));
							break;
						case 'vLinkOut':
							$novoXML_route->appendChild( $novoXML->createElement( "vLinkOut", $i->nodeValue ));
							break;
						case '#text':
							break;
						default:
							throw new RuntimeException('Impossivel converter arquivo. O elemento '.$i->nodeName.' não existe na linguagem.');
							break;
					}
				}
			}
		}
		$xml = new DOMDocument();				
		if($xml->load($arq)) {
			$xml->preserveWhiteSpace = false;
			$elementos = $xml->getElementsByTagName('*');
			foreach ($elementos AS $item) {
				switch ($item->nodeName) {
					case 'Definitions':
						constroi_description();
						break;
					case 'ServiceTemplate':
						constroi_virtualInfrastructure($item);
						break;
					default:
						break;
				}
			}
			$novoNome = '../convertidos/VXDL_'.$_REQUEST['arq']; 
			//$novoXML->save($novoNome);	
			if($novoXML->save($novoNome)){
				$msg = conecta();
					if($msg != 'Conectado'){
					throw new RuntimeException("Erro ".$msg) ;
				}else{
					$nomeArq = 'VXDL_'.$_REQUEST['arq'];
					$sql = "update arquivo set arqconvertido = '".$nomeArq."' where arqusu=".$_SESSION['usuarioid']." and arqnome='".$_REQUEST['arqOrig']."'";
					$result = mysql_query($sql);
					// Verifica o resultado.
					if (!$result) {
						throw new RuntimeException('Impossivel salvar dados no servidor.');
					}else{
						throw new RuntimeException('Arquivo convertido com sucesso.');
					}
				}				
	      	}
		}else {
	        throw new RuntimeException('Erro ao abrir o arquivo\n');
	    }
	} catch (RuntimeException $e) {
		echo $e->getMessage();
	}		
?>
