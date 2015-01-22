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
	//Variaveis globais
	$novoXML_Definitions					= NULL;
	$novoXML_ServiceTemplate  	  			= NULL;
	$novoXML_ServiceTemplateTags			= NULL;
	$novoXML_STTag							= NULL;
	$novoXML_TopologyTemplate 	  			= NULL;
	$novoXML_NodeTemplate 	  	  			= NULL;
	$novoXML_NodeTemplateProperties 		= NULL;
	$novoXML_RelationshipTemplate 			= NULL;
	$novoXML_RelationshipTemplateProperties	= NULL;
	$novoXML_capacity		  	  			= NULL;
	$novoXML_bandwidth			  			= NULL;
	$novoXML_cpu			 	  			= NULL;
	$novoXML_interval		  	  			= NULL;
	$novoXML_scale			  	  			= NULL;
	$novoXML_storage		  	  			= NULL;
	$novoXML_loadBalancer					= NULL;
	$novoXML_nat							= NULL;
	$novoXML_masquerade						= NULL;
	$novoXML_controlPlane					= NULL;
	$novoXML_routingTable					= NULL;
	$novoXML_route							= NULL;
	$novoXML_tag							= NULL;
	$vNodeP		   							= FALSE;
	$vStorageP								= FALSE;
	$vRouterP								= FALSE;
	$vAccessPointP							= FALSE;
	$relationshipP 							= TRUE;
	//Array para guardar os NodeTemplates
	$arrNT = array();	
	try{			
		/*
		 * Atributos do ServiceTemplate
		 * id="xs:ID"
		 * name="xs:string"?
		 * targetNamespace="xs:anyURI"
		 * substitutableNodeType="xs:QName"?
		 */		 
		function cria_Definitions(){
			global $novoXML_Definitions, $novoXML;
			$novoXML_Definitions = $novoXML->createElement( "tosca:Definitions" );
			$novoXML_Definitions->setAttribute( "id", "TOSCADefinition" );
			$novoXML_Definitions->setIdAttribute("id",TRUE);			
			$novoXML_Definitions->setAttribute( "xmlns:tosca","http://docs.oasis-open.org/tosca/ns/2011/12"); 
			$novoXML_Definitions->setAttribute( "targetNamespace","http://docs.oasis-open.org/tosca/ns/2011/12/ToscaSpecificTypes");
			//$novoXML_Definitions->setAttribute( "xmlns:my","http://vtconverter.esy.es/Definitions/");			
			
			$novoXML_Import = $novoXML->createElement( "tosca:Import" );
			$novoXML_Import->setAttribute( "importType", "http://www.w3.org/2001/XMLSchema" );
			$novoXML_Import->setAttribute( "namespace", "http://www.example.com/tcc/udesc" );
			$novoXML_Import->setAttribute( "location", "../imports/Definitions.xsd"); 			
			$novoXML_Definitions->appendChild( $novoXML_Import );
			$novoXML->appendChild( $novoXML_Definitions );
		}
		function constroi_ServiceTemplate($id = NULL, $name = NULL, $targetNamespace = NULL , $substitutableNodeType = NULL){
			global $novoXML, $novoXML_ServiceTemplate, $novoXML_Definitions,$novoXML_STTag,$novoXML_ServiceTemplateTags;
			//Crio o elemento ServiceTemplate
			$novoXML_ServiceTemplate = $novoXML->createElement( "tosca:ServiceTemplate" );			
			$novoXML_ServiceTemplateTags = $novoXML->createElement( "tosca:Tags" );
			$novoXML_ServiceTemplate->appendChild($novoXML_ServiceTemplateTags);			
			//Adiciono os atrbutos
			$novoXML_ServiceTemplate->setAttribute( "id", $id );
			$novoXML_ServiceTemplate->setIdAttribute("id",TRUE);					
			if (!empty($name)) {
				$novoXML_ServiceTemplate->setAttribute( "name", $id );
			}			
			if (!empty($targetNamespace)) {
				$novoXML_ServiceTemplate->setAttribute( "targetNamespace", $targetNamespace );
			}			
			if (!empty($substitutableNodeType)) {
				$novoXML_ServiceTemplate->setAttribute( "substitutableNodeType", $substitutableNodeType );
			}			
			//Adiciono o ServiceTemplate ao Definitions Document
			$novoXML_Definitions->appendChild( $novoXML_ServiceTemplate );		
		}		
		function constroi_ServiceTemplate_Tags($p){
			global $novoXML, $novoXML_ServiceTemplate,$novoXML_ServiceTemplateTags,$novoXML_STTag;			
			$count = 0;					
			if ($p->hasChildNodes()) {
		    	$p_filhos = $p->childNodes;
		        foreach($p_filhos as $i) {	            
					switch ($i->nodeName) {
						case 'id':
							//Adiciono o atributo id
							$novoXML_ServiceTemplate->setAttribute( "id", $i->nodeValue );
							$novoXML_ServiceTemplate->setIdAttribute("id",TRUE);
							break;
						case 'location':
							$novoXML_STTag = $novoXML->createElement( "tosca:Tag" );
							$novoXML_STTag->setAttribute( "name", "location" );
							$novoXML_STTag->setAttribute( "value", $i->nodeValue );
							$novoXML_ServiceTemplateTags->appendChild($novoXML_STTag);
							$count++;
							break;
						case 'startDate':
							$novoXML_STTag = $novoXML->createElement( "tosca:Tag" );
							$novoXML_STTag->setAttribute( "name", "startDate" );
							$novoXML_STTag->setAttribute( "value", $i->nodeValue );
							$novoXML_ServiceTemplateTags->appendChild($novoXML_STTag);
							$count++;
							break;
						case 'totalTime':
							$novoXML_STTag = $novoXML->createElement( "tosca:Tag" );
							$novoXML_STTag->setAttribute( "name", "totalTime" );
							$novoXML_STTag->setAttribute( "value", $i->nodeValue );
							$novoXML_ServiceTemplateTags->appendChild($novoXML_STTag);
							$count++;
							break;
						case 'model':
							$novoXML_STTag = $novoXML->createElement( "tosca:Tag" );
							$novoXML_STTag->setAttribute( "name", "model" );
							$novoXML_STTag->setAttribute( "value", $i->nodeValue );
							$novoXML_ServiceTemplateTags->appendChild($novoXML_STTag);
							$count++;
							break;
						case 'exclusivity':
							$novoXML_STTag = $novoXML->createElement( "tosca:Tag" );
							$novoXML_STTag->setAttribute( "name", "exclusivity" );
							$novoXML_STTag->setAttribute( "value", $i->nodeValue );
							$novoXML_ServiceTemplateTags->appendChild($novoXML_STTag);
							$count++;
							break;
						case 'tag':
							$novoXML_STTag = $novoXML->createElement( "tosca:Tag" );
							$novoXML_STTag->setAttribute( "name", "tag" );
							$novoXML_STTag->setAttribute( "value", $i->nodeValue );
							$novoXML_ServiceTemplateTags->appendChild($novoXML_STTag);
							$count++;
							break;
						case 'owner':
							$novoXML_STTag = $novoXML->createElement( "tosca:Tag" );
							$novoXML_STTag->setAttribute( "name", "owner" );
							$novoXML_STTag->setAttribute( "value", $i->nodeValue );
							$novoXML_ServiceTemplateTags->appendChild($novoXML_STTag);
							$count++;
							break;
						case 'user':
							$novoXML_STTag = $novoXML->createElement( "tosca:Tag" );
							$novoXML_STTag->setAttribute( "name", "user" );
							$novoXML_STTag->setAttribute( "value", $i->nodeValue );
							$novoXML_ServiceTemplateTags->appendChild($novoXML_STTag);
							$count++;
							break;/**/
						case '#text':
							break;
						default:
							//throw new RuntimeException('Impossivel converter arquivo. O elemento '.$i->nodeName.' não existe na linguagem. (ST)');
							break;
					}
				}
			}
			//Caso a variavel count seja menor que zero, significa que não existem propriedades
			if ($count <= 0) {
				$novoXML_ServiceTemplate->removeChild($novoXML_ServiceTemplateTags);			
			}
		}		
		/*
		 * TopologyTemplate não possui atributos
		 */
		function constroi_TopologyTemplate()
		{
			global $novoXML, $novoXML_TopologyTemplate, $novoXML_ServiceTemplate;
			$novoXML_TopologyTemplate = $novoXML->createElement( "tosca:TopologyTemplate" );
			//Adiciono o TopologyTemplate ao ServiceTemplate
			$novoXML_ServiceTemplate->appendChild( $novoXML_TopologyTemplate );
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
						case '#text':
							break;
						default:
							//throw new RuntimeException('Impossivel converter arquivo. O elemento '.$i->nodeName.' não existe na linguagem. (tag)');
							break;
					}
				}
			}
		}
		/*
		 * Atributos do NodeTemplate
		 * id="xs:ID" name="xs:string"? type="xs:QName"
	     * minInstances="xs:integer"?
	     * maxInstances="xs:integer | xs:string"?
		 */	
		function constroi_NodeTemplate_vNode($vNode){
			global $novoXML_NodeTemplate, $novoXML_NodeTemplateProperties,$novoXML, $novoXML_TopologyTemplate, $novoXML_capacity,$novoXML_cpu, $novoXML_storage, $vNodeP, $arrNT,$novoXML_tag;
			$count = 0; // Contador
			//Crio o elemento NodeTemplate
			$novoXML_NodeTemplate = $novoXML->createElement( "tosca:NodeTemplate" );
			$novoXML_NodeTemplateProperties = $novoXML->createElement( "tosca:Properties" );
			$novoXML_NTProperties = $novoXML->createElement( "ns1:vNodeProperties" );
			$novoXML_NTProperties->setAttribute("xmlns:ns1","http://www.example.com/tcc/udesc");
            $novoXML_NTProperties->setAttribute("xmlns","http://www.example.com/tcc/udesc");
			$novoXML_NodeTemplate->setAttribute("xmlns:tst","http://docs.oasis-open.org/tosca/ns/2011/12/ToscaSpecificTypes");
			if ($vNode->hasChildNodes()) {
		    	$vNode_filhos = $vNode->childNodes;
		        foreach($vNode_filhos as $i) {	            
					switch ($i->nodeName) {
						case 'id':
							$arrNT[$i->nodeValue] = 'NodeTemplate'; //Salvo o id do NodeTemplate
							//$count += constroi_Comum($vNode, $novoXML_NodeTemplate, $novoXML_NodeTemplateProperties,$novoXML_NTProperties);
							//Adiciono o atributo type
							$novoXML_NodeTemplate->setAttribute( "id", $i->nodeValue );
							$novoXML_NodeTemplate->setIdAttribute("id",TRUE);
							$novoXML_NodeTemplate->setAttribute( "type", "tst:TOSCAvNodeType" );
							break;
						case 'location':
							$novoXML_NTProperties->appendChild($novoXML->createElement( "location", $i->nodeValue ));
							$count++;
							break;
						case 'startDate':
							$novoXML_NTProperties->appendChild($novoXML->createElement( "startDate", $i->nodeValue ));
							$count++;
							break;
						case 'totalTime':
							$novoXML_NTProperties->appendChild($novoXML->createElement( "totalTime", $i->nodeValue ));
							$count++;
							break;
						case 'model':
							$novoXML_NTProperties->appendChild($novoXML->createElement( "model", $i->nodeValue ));
							$count++;
							break;
						case 'exclusivity':
							$novoXML_NTProperties->appendChild($novoXML->createElement( "exclusivity", $i->nodeValue ));
							$count++;
							break;
						case 'tag':
							$novoXML_tag = $novoXML->createElement( "tag" );
							constroi_tag($i);
							$novoXML_NTProperties->appendChild($novoXML_tag);
							$count++;
							break;
						case 'device':
							$novoXML_NTProperties->appendChild($novoXML->createElement( "device", $i->nodeValue ));
							$count++;
							break;
						case 'image':
							$novoXML_NTProperties->appendChild($novoXML->createElement( "image", $i->nodeValue ));
							$count++;
							break;
						case 'region':
							$novoXML_NTProperties->appendChild($novoXML->createElement( "region", $i->nodeValue ));
							$count++;
							break;
						case 'memory':
							$novoXML_capacity = $novoXML->createElement( "memory" );
							constroi_Capacity($i);
							$novoXML_NTProperties->appendChild($novoXML_capacity);
							$count++;
							break;				
						case 'storage':
							$novoXML_capacity = $novoXML->createElement( "storage" );
							constroi_Capacity($i);
							$novoXML_NTProperties->appendChild($novoXML_capacity);
							$count++;
							break;
						case 'cpu':
							$novoXML_cpu = $novoXML->createElement( "cpu" );
							constroi_CPU($i);
							$novoXML_NTProperties->appendChild($novoXML_cpu);
							$count++;
							break;
						case '#text':
							break;
						default:
							//throw new RuntimeException('Impossivel converter arquivo. O elemento '.$i->nodeName.' não existe na linguagem. (vNode)');
							break;
					}
				}
			}
			//Caso a variavel count seja maior que zero, significa que existem propriedades
			if ($count > 0) {
				$novoXML_NodeTemplateProperties->appendChild($novoXML_NTProperties);
				$novoXML_NodeTemplate->appendChild($novoXML_NodeTemplateProperties);
				$vNodeP = TRUE;	
			}
			//Adiciono o novo NodeTemplate dentro do TopologyTemplate
			$novoXML_TopologyTemplate->appendChild( $novoXML_NodeTemplate );
		}		
		function constroi_NodeTemplate_vStorage($vStorage){
			global $novoXML, $novoXML_NodeTemplate, $novoXML_NodeTemplateProperties, $vStorageP, $arrNT,$novoXML_tag;
			$count = 0; // Contador			
			//Crio o elemento NodeTemplate
			$novoXML_NodeTemplate = $novoXML->createElement( "tosca:NodeTemplate" );
			$novoXML_NodeTemplateProperties = $novoXML->createElement( "tosca:Properties" );
			$novoXML_NTProperties = $novoXML->createElement( "ns1:vStorageProperties" );
			$novoXML_NTProperties->setAttribute("xmlns:ns1","http://www.example.com/tcc/udesc");
            $novoXML_NTProperties->setAttribute("xmlns","http://www.example.com/tcc/udesc");
			$novoXML_NodeTemplate->setAttribute("xmlns:tst","http://docs.oasis-open.org/tosca/ns/2011/12/ToscaSpecificTypes");			
			if ($vStorage->hasChildNodes()) {
		    	$vStorage_filhos = $vStorage->childNodes;
		        foreach($vStorage_filhos as $i) {	            
					switch ($i->nodeName) {
						case 'id':
							$arrNT[$i->nodeValue] = 'NodeTemplate'; //Salvo o id do NodeTemplate
							//$count += constroi_Comum($vStorage, $novoXML_NodeTemplate, $novoXML_NodeTemplateProperties,$novoXML_NTProperties);							
							$novoXML_NodeTemplate->setAttribute( "id", $i->nodeValue );
							$novoXML_NodeTemplate->setIdAttribute("id",TRUE);
							$novoXML_NodeTemplate->setAttribute( "type", "tst:TOSCAvStorageType" );
							break;
						case 'location':
							$novoXML_NTProperties->appendChild($novoXML->createElement( "location", $i->nodeValue ));
							$count++;
							break;
						case 'startDate':
							$novoXML_NTProperties->appendChild($novoXML->createElement( "startDate", $i->nodeValue ));
							$count++;
							break;
						case 'totalTime':
							$novoXML_NTProperties->appendChild($novoXML->createElement( "totalTime", $i->nodeValue ));
							$count++;
							break;
						case 'model':
							$novoXML_NTProperties->appendChild($novoXML->createElement( "model", $i->nodeValue ));
							$count++;
							break;
						case 'exclusivity':
							$novoXML_NTProperties->appendChild($novoXML->createElement( "exclusivity", $i->nodeValue ));
							$count++;
							break;
						case 'tag':
							$novoXML_tag = $novoXML->createElement( "tag" );
							constroi_tag($i);
							$novoXML_NTProperties->appendChild($novoXML_tag);
							$count++;
							break;
						case 'type':
							$novoXML_NTProperties->appendChild($novoXML->createElement( "type", $i->nodeValue ));
							$count++;
							break;
						case 'image':
							$novoXML_NTProperties->appendChild($novoXML->createElement( "image", $i->nodeValue ));
							$count++;
							break;
						case 'region':
							$novoXML_NTProperties->appendChild($novoXML->createElement( "region", $i->nodeValue ));
							$count++;
							break;
						case 'size':
							$novoXML_capacity = $novoXML->createElement( "size" );
							constroi_Capacity($i);
							$novoXML_NTProperties->appendChild($novoXML_capacity);
							$count++;
							break;
						case '#text':
							break;
						default:
							//throw new RuntimeException('(vStorage) Impossivel converter arquivo. O elemento '.$i->nodeName.' não existe na linguagem.');
							break;
					}
				}
			}
			//Caso a variavel count seja maior que zero, significa que existem propriedades
			if ($count > 0) {
				$novoXML_NodeTemplateProperties->appendChild($novoXML_NTProperties);
				$novoXML_NodeTemplate->appendChild($novoXML_NodeTemplateProperties);
				$vStorageP = TRUE;	
			}
			//Adiciono o novo NodeTemplate dentro do TopologyTemplate
			$novoXML_TopologyTemplate->appendChild( $novoXML_NodeTemplate );
		}		
		function constroi_NodeTemplate_vRouter($vRouter){
			global $novoXML, $novoXML_NodeTemplate, $novoXML_NodeTemplateProperties, $novoXML_capacity, $vRouterP, $arrNT, $novoXML_controlPlane, $novoXML_TopologyTemplate,$novoXML_tag;
			$count = 0; // Contador
			//Crio o elemento NodeTemplate
			$novoXML_NodeTemplate = $novoXML->createElement( "tosca:NodeTemplate" );
			$novoXML_NodeTemplateProperties = $novoXML->createElement( "tosca:Properties" );
			$novoXML_NTProperties = $novoXML->createElement( "ns1:vRouterProperties" );
			$novoXML_NTProperties->setAttribute("xmlns:ns1","http://www.example.com/tcc/udesc");
            $novoXML_NTProperties->setAttribute("xmlns","http://www.example.com/tcc/udesc");
			$novoXML_NodeTemplate->setAttribute("xmlns:tst","http://docs.oasis-open.org/tosca/ns/2011/12/ToscaSpecificTypes");
			if ($vRouter->hasChildNodes()) {
		    	$vRouter_filhos = $vRouter->childNodes;
		        foreach($vRouter_filhos as $i) {	            
					switch ($i->nodeName) {
						case 'id':
							$arrNT[$i->nodeValue] = 'NodeTemplate'; //Salvo o id do NodeTemplate
							//$count += constroi_Comum($vStorage, $novoXML_NodeTemplate, $novoXML_NodeTemplateProperties,$novoXML_NTProperties);							
							$novoXML_NodeTemplate->setAttribute( "id", $i->nodeValue );
							$novoXML_NodeTemplate->setIdAttribute("id",TRUE);
							$novoXML_NodeTemplate->setAttribute( "type", "tst:TOSCAvRouterType" );
							break;
						case 'location':
							$novoXML_NTProperties->appendChild($novoXML->createElement( "location", $i->nodeValue ));
							$count++;
							break;
						case 'startDate':
							$novoXML_NTProperties->appendChild($novoXML->createElement( "startDate", $i->nodeValue ));
							$count++;
							break;
						case 'totalTime':
							$novoXML_NTProperties->appendChild($novoXML->createElement( "totalTime", $i->nodeValue ));
							$count++;
							break;
						case 'model':
							$novoXML_NTProperties->appendChild($novoXML->createElement( "model", $i->nodeValue ));
							$count++;
							break;
						case 'exclusivity':
							$novoXML_NTProperties->appendChild($novoXML->createElement( "exclusivity", $i->nodeValue ));
							$count++;
							break;
						case 'tag':
							$novoXML_tag = $novoXML->createElement( "tag" );
							constroi_tag($i);
							$novoXML_NTProperties->appendChild($novoXML_tag);
							$count++;
							break;
						case 'region':
							$novoXML_NTProperties->appendChild($novoXML->createElement( "region", $i->nodeValue ));
							$count++;
							break;
						case 'memory':
							$novoXML_capacity = $novoXML->createElement( "memory" );
							constroi_Capacity($i);
							$novoXML_NTProperties->appendChild($novoXML_capacity);
							$count++;
							break;
						case 'controlPlane':
							$novoXML_controlPlane = $novoXML->createElement( "controlPlane" );
							constroi_controlPlane($i);
							$novoXML_NTProperties->appendChild($novoXML_controlPlane);
							$count++;
							break;
						case '#text':
							break;
						default:
							//throw new RuntimeException('(vRouter) Impossivel converter arquivo. O elemento '.$i->nodeName.' não existe na linguagem.');
							break;
					}
				}
			}
			//Caso a variavel count seja maior que zero, significa que existem propriedades
			if ($count > 0) {
				$novoXML_NodeTemplateProperties->appendChild($novoXML_NTProperties);
				$novoXML_NodeTemplate->appendChild($novoXML_NodeTemplateProperties);
				$vRouterP = TRUE;	
			}
			//Adiciono o novo NodeTemplate dentro do TopologyTemplate
			$novoXML_TopologyTemplate->appendChild( $novoXML_NodeTemplate );
		}		
		function constroi_NodeTemplate_vAccessPoint($vAccessPoint){
			global $novoXML, $novoXML_NodeTemplate, $novoXML_NodeTemplateProperties, $novoXML_capacity, $vAccessPointP, $arrNT, $novoXML_loadBalancer, $novoXML_nat, $novoXML_masquerade, $novoXML_TopologyTemplate,$novoXML_tag;
			$count = 0; // Contador
			//Crio o elemento NodeTemplate
			$novoXML_NodeTemplate = $novoXML->createElement( "tosca:NodeTemplate" );
			$novoXML_NodeTemplateProperties = $novoXML->createElement( "tosca:Properties" );
			$novoXML_NTProperties = $novoXML->createElement( "ns1:vAccessPointProperties" );
			$novoXML_NTProperties->setAttribute("xmlns:ns1","http://www.example.com/tcc/udesc");
            $novoXML_NTProperties->setAttribute("xmlns","http://www.example.com/tcc/udesc");
			$novoXML_NodeTemplate->setAttribute("xmlns:tst","http://docs.oasis-open.org/tosca/ns/2011/12/ToscaSpecificTypes");			
			if ($vAccessPoint->hasChildNodes()) {
		    	$vAccessPoint_filhos = $vAccessPoint->childNodes;
		        foreach($vAccessPoint_filhos as $i) {	            
					switch ($i->nodeName) {
						case 'id':
							$arrNT[$i->nodeValue] = 'NodeTemplate'; //Salvo o id do NodeTemplate
							//$count += constroi_Comum($vStorage, $novoXML_NodeTemplate, $novoXML_NodeTemplateProperties,$novoXML_NTProperties);							
							$novoXML_NodeTemplate->setAttribute( "id", $i->nodeValue );
							$novoXML_NodeTemplate->setIdAttribute("id",TRUE);
							$novoXML_NodeTemplate->setAttribute( "type", "tst:TOSCAvAccessPointType" );
							break;
						case 'location':
							$novoXML_NTProperties->appendChild($novoXML->createElement( "location", $i->nodeValue ));
							$count++;
							break;
						case 'startDate':
							$novoXML_NTProperties->appendChild($novoXML->createElement( "startDate", $i->nodeValue ));
							$count++;
							break;
						case 'totalTime':
							$novoXML_NTProperties->appendChild($novoXML->createElement( "totalTime", $i->nodeValue ));
							$count++;
							break;
						case 'model':
							$novoXML_NTProperties->appendChild($novoXML->createElement( "model", $i->nodeValue ));
							$count++;
							break;
						case 'exclusivity':
							$novoXML_NTProperties->appendChild($novoXML->createElement( "exclusivity", $i->nodeValue ));
							$count++;
							break;
						case 'tag':
							$novoXML_tag = $novoXML->createElement( "tag" );
							constroi_tag($i);
							$novoXML_NTProperties->appendChild($novoXML_tag);
							$count++;
							break;
						case 'region':
							$novoXML_NTProperties->appendChild($novoXML->createElement( "region", $i->nodeValue ));
							$count++;
							break;
						case 'loadBalancer':
							$novoXML_loadBalancer = $novoXML->createElement( "loadBalancer" );
							constroi_loadBalancer($i);
							$novoXML_NTProperties->appendChild($novoXML_loadBalancer);
							$count++;
							break;
						case 'nat':
							$novoXML_nat = $novoXML->createElement( "nat" );
							constroi_nat($i);
							$novoXML_NTProperties->appendChild($novoXML_nat);
							$count++;
							break;
						case 'masquerade':
							$novoXML_masquerade = $novoXML->createElement( "masquerade" );
							constroi_masquerade($i);
							$novoXML_NTProperties->appendChild($novoXML_masquerade);
							$count++;
							break;
						case '#text':
							break;
						default:
							//throw new RuntimeException('(ap) Impossivel converter arquivo. O elemento '.$i->nodeName.' não existe na linguagem.');
							break;
					}
				}
			}
			//Caso a variavel count seja maior que zero, significa que existem propriedades
			if ($count > 0) {
				$novoXML_NodeTemplateProperties->appendChild($novoXML_NTProperties);
				$novoXML_NodeTemplate->appendChild($novoXML_NodeTemplateProperties);
				$vAccessPointP = TRUE;	
			}
			//Adiciono o novo NodeTemplate dentro do TopologyTemplate
			$novoXML_TopologyTemplate->appendChild( $novoXML_NodeTemplate );
		}	
		function constroi_RelationshipTemplate($vLink){
			global $novoXML_RelationshipTemplate, $novoXML_capacity, $novoXML, $novoXML_bandwidth,$novoXML_TopologyTemplate,$novoXML_RelationshipTemplateProperties, $relationshipP, $arrNT,$novoXML_tag;
			$count = 0; // Contador			
			$novoXML_RelationshipTemplate = $novoXML->createElement( "tosca:RelationshipTemplate" );
			$novoXML_RelationshipTemplateProperties = $novoXML->createElement( "tosca:Properties" );
			$novoXML_RTProperties = $novoXML->createElement( "ns1:vLinkProperties" );		
			$novoXML_RelationshipTemplateProperties->appendChild($novoXML_RTProperties);
			$novoXML_RelationshipTemplate->insertBefore($novoXML_RelationshipTemplateProperties);
			$novoXML_RTProperties->setAttribute("xmlns:ns1","http://www.example.com/tcc/udesc");
            $novoXML_RTProperties->setAttribute("xmlns","http://www.example.com/tcc/udesc");
			$novoXML_RelationshipTemplate->setAttribute("xmlns:tst","http://docs.oasis-open.org/tosca/ns/2011/12/ToscaSpecificTypes");
			
			if ($vLink->hasChildNodes()) {
		    	$vLink_filhos = $vLink->childNodes;
		        foreach($vLink_filhos as $i) {
		        	switch ($i->nodeName) {
						case 'id':
							//$arrNT[$i->nodeValue] = 'RelationshipTemplate'; //Salvo o id do NodeTemplate
							//$count += constroi_Comum($vStorage, $novoXML_NodeTemplate, $novoXML_NodeTemplateProperties,$novoXML_NTProperties);							
							$novoXML_RelationshipTemplate->setAttribute( "id", $i->nodeValue );
							$novoXML_RelationshipTemplate->setIdAttribute("id",TRUE);
							$novoXML_RelationshipTemplate->setAttribute( "type", "tst:TOSCAvLinkType" );
							break;
						case 'location':
							$novoXML_NTProperties->appendChild($novoXML->createElement( "location", $i->nodeValue ));
							$count++;
							break;
						case 'startDate':
							$novoXML_NTProperties->appendChild($novoXML->createElement( "startDate", $i->nodeValue ));
							$count++;
							break;
						case 'totalTime':
							$novoXML_NTProperties->appendChild($novoXML->createElement( "totalTime", $i->nodeValue ));
							$count++;
							break;
						case 'model':
							$novoXML_NTProperties->appendChild($novoXML->createElement( "model", $i->nodeValue ));
							$count++;
							break;
						case 'exclusivity':
							$novoXML_NTProperties->appendChild($novoXML->createElement( "exclusivity", $i->nodeValue ));
							$count++;
							break;
						case 'tag':
							$novoXML_tag = $novoXML->createElement( "tag" );
							constroi_tag($i);
							$novoXML_NTProperties->appendChild($novoXML_tag);
							$count++;
							break;
						case 'source':
							//Valido se o elemento source é um NodeTemplate
							//if (array_key_exists ( $i->nodeValue ,$arrNT ) && isset($arrNT[$i->nodeValue])) {
								$SourceElement = $novoXML->createElement( "tosca:SourceElement");
								$SourceElement->setAttribute( "ref", $i->nodeValue );
								$novoXML_RelationshipTemplate->appendChild( $SourceElement );								
							//}else{
							//	throw new RuntimeException('Elemento '.$i->nodeValue.' não pode ser utlizado como source no RelationshipTemplate');
							//}
							break;
						case 'destination':
							//Valido se o elemento destination é um NodeTemplate
							//if (array_key_exists ( $i->nodeValue ,$arrNT ) && isset($arrNT[$i->nodeValue])) {
								$TargetElement = $novoXML->createElement( "tosca:TargetElement");
								$TargetElement->setAttribute( "ref", $i->nodeValue );
								$novoXML_RelationshipTemplate->appendChild( $TargetElement );
							//}else{
							//	throw new RuntimeException('Elemento '.$i->nodeValue.' não pode ser utlizado como destination no RelationshipTemplate');
							//}
							break;
						case 'bandwidth':
							$novoXML_bandwidth = $novoXML->createElement( "bandwidth" );
							constroi_bandwidth($i);
							$novoXML_RTProperties->appendChild($novoXML_bandwidth);
							$count++;
							break;
						case 'latency':
							$novoXML_capacity = $novoXML->createElement( "latency" );
							constroi_Capacity($i);
							$novoXML_RTProperties->appendChild($novoXML_capacity);
							$count++;
							break;/**/
						case '#text':
							break;
						default:
							//throw new RuntimeException('(RT) Impossivel converter arquivo. O elemento '.$i->nodeName.' não existe na linguagem.');
							break;
					}
		    	}
		    }
			//Caso a variavel count seja menor que zero, significa que não existem propriedades
			if ($count <= 0) {
				$novoXML_RelationshipTemplate->removeChild($novoXML_RelationshipTemplateProperties);
				$relationshipP = FALSE;
			}
			//Adiciono o novo RelationshipTemplate dentro do TopologyTemplate
			$novoXML_TopologyTemplate->appendChild( $novoXML_RelationshipTemplate );
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
							//throw new RuntimeException('(bandwidth) Impossivel converter arquivo. O elemento '.$i->nodeName.' não existe na linguagem.');
							break;
					}
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
							//throw new RuntimeException('(cpu)Impossivel converter arquivo. O elemento '.$i->nodeName.' não existe na linguagem.');
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
							//throw new RuntimeException('(capacity)Impossivel converter arquivo. O elemento '.$i->nodeName.' não existe na linguagem.');
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
							//throw new RuntimeException('(interval)Impossivel converter arquivo. O elemento '.$i->nodeName.' não existe na linguagem.');
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
							//throw new RuntimeException('(scale)Impossivel converter arquivo. O elemento '.$i->nodeName.' não existe na linguagem.');
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
							//throw new RuntimeException('(lb)Impossivel converter arquivo. O elemento '.$i->nodeName.' não existe na linguagem.');
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
							//throw new RuntimeException('(nat)Impossivel converter arquivo. O elemento '.$i->nodeName.' não existe na linguagem.');
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
							//throw new RuntimeException('(mqq)Impossivel converter arquivo. O elemento '.$i->nodeName.' não existe na linguagem.');
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
							//throw new RuntimeException('(cplane)Impossivel converter arquivo. O elemento '.$i->nodeName.' não existe na linguagem.');
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
							//throw new RuntimeException('(rtable)Impossivel converter arquivo. O elemento '.$i->nodeName.' não existe na linguagem.');
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
							//throw new RuntimeException('(route)Impossivel converter arquivo. O elemento '.$i->nodeName.' não existe na linguagem.');
							break;
					}
				}
			}
		}
		function vaida_Conexoes($el){
			global $arrNT;
			$source = "-1";
			$destination = "-2";
			foreach ($el AS $item) {		
				switch ($item->nodeName) {
					case 'vLink':
						if ($item->hasChildNodes()) {
					    	$vLink_filhos = $item->childNodes;
					        foreach($vLink_filhos as $i) {
					        	switch ($i->nodeName) {
					        		case 'source':
										//Valido se o elemento source é um NodeTemplate
										if (!array_key_exists ( $i->nodeValue ,$arrNT ) && !isset($arrNT[$i->nodeValue])) {
											throw new RuntimeException('Elemento '.$i->nodeValue.' não pode ser utlizado como source no RelationshipTemplate');
										}else{
											$source = $i->nodeValue;
										}
										break;
									case 'destination':
										//Valido se o elemento destination é um NodeTemplate
										if (!array_key_exists( $i->nodeValue ,$arrNT ) && !isset($arrNT[$i->nodeValue])) {
											throw new RuntimeException('Elemento '.$i->nodeValue.' não pode ser utlizado como destination no RelationshipTemplate');
										}else{
											$destination = $i->nodeValue;
										}
										break;
									default:
										break;
								}
							}
						}
						break;
					case 'loadBalancer':
						
						break;
					case 'nat':
						if ($item->hasChildNodes()) {
					    	$nat_filhos = $item->childNodes;
					        foreach($nat_filhos as $i) {
					        	switch ($i->nodeName) {
					        		case 'inEndpoint':
										//Valido se o elemento source é um NodeTemplate
										if (!array_key_exists ( $i->nodeValue ,$arrNT ) && !isset($arrNT[$i->nodeValue])) {
											throw new RuntimeException('Elemento '.$i->nodeValue.' não pode ser utlizado como inEndpoint no elemento nat');
										}
										break;
									default:
										break;
								}
							}
						}
						break;
					case 'masquerade':
						if ($item->hasChildNodes()) {
					    	$masquerade_filhos = $item->childNodes;
					        foreach($masquerade_filhos as $i) {
					        	switch ($i->nodeName) {
					        		case 'inEndpoint':
										//Valido se o elemento source é um NodeTemplate
										if (!array_key_exists ( $i->nodeValue ,$arrNT ) && !isset($arrNT[$i->nodeValue])) {
											throw new RuntimeException('Elemento '.$i->nodeValue.' não pode ser utlizado como inEndpoint no elemento masquerade');
										}
										break;
									default:
										break;
								}
							}
						}
						break;
					case 'route':
						if ($item->hasChildNodes()) {
					    	$route_filhos = $item->childNodes;
					        foreach($route_filhos as $i) {
					        	switch ($i->nodeName) {
					        		case 'source':
										//Valido se o elemento source é um NodeTemplate
										if (!array_key_exists ( $i->nodeValue ,$arrNT ) && !isset($arrNT[$i->nodeValue])) {
											throw new RuntimeException('Elemento '.$i->nodeValue.' não pode ser utlizado como source no elemento route');
										}
										break;
									case 'destination':
										//Valido se o elemento destination é um NodeTemplate
										if (!array_key_exists( $i->nodeValue ,$arrNT ) && !isset($arrNT[$i->nodeValue])) {
											throw new RuntimeException('Elemento '.$i->nodeValue.' não pode ser utlizado como destination no elemento route');
										}
										break;
									case 'vLinkIn':
										throw new RuntimeException('Elemento '.$i->nodeValue.' não pode ser convertido');
										break;
									case 'vLinkOut':
										throw new RuntimeException('Elemento '.$i->nodeValue.' não pode ser convertido');
										break;
									default:
										break;
								}
							}
						}
						break;
					case '#text':						
						break;
					default:
						break;
				}
			}
			if($source == $destination){
				throw new RuntimeException('Elemento source e destination não podem ser iguais.');
			}
		}
		$xml = new DOMDocument();			
		if($xml->load($arq)) {
			//Descarta espaços em branco
			$xml->preserveWhiteSpace = false;			
			$elementos = $xml->getElementsByTagName('*');			
			foreach ($elementos AS $item) {		
				switch ($item->nodeName) {
					case 'description':
						cria_Definitions();
						constroi_ServiceTemplate();
						break;
					case 'virtualInfrastructure':
						constroi_TopologyTemplate();
						constroi_ServiceTemplate_Tags($item);
						break;
					case 'vNode':
						constroi_NodeTemplate_vNode($item);
						break;
					case 'vStorage':
						constroi_NodeTemplate_vStorage($item);
						break;
					case 'vLink':
						constroi_RelationshipTemplate($item);
						break;
					case 'vRouter':
						constroi_NodeTemplate_vRouter($item);
						break;
					case 'vAccessPoint':
						constroi_NodeTemplate_vAccessPoint($item);
						break;
					case '#text':
						break;
					default:
						//throw new RuntimeException('Impossivel converter arquivo. O elemento '.$item->nodeName.' não existe na linguagem.');
						break;
				}
			}
			if ($vNodeP) {
				$novoXML_NodeType = $novoXML->createElement( "tosca:NodeType" );
				$novoXML_NodeType->setAttribute( "name", "TOSCAvNodeType" );				
				$novoXML_nodePD = $novoXML->createElement( "tosca:PropertiesDefinition" );
				$novoXML_nodePD->setAttribute( "element", "ns2:vNodeProperties" );
				$novoXML_nodePD->setAttribute( "xmlns:ns2","http://www.example.com/tcc/udesc" );
				$novoXML_NodeType->appendChild( $novoXML_nodePD );				
				$novoXML_Definitions->appendChild( $novoXML_NodeType );
			}
			if($vStorageP){
				$novoXML_NodeType = $novoXML->createElement( "tosca:NodeType" );
				$novoXML_NodeType->setAttribute( "name", "TOSCAvStorageType" );				
				$novoXML_nodePD = $novoXML->createElement( "tosca:PropertiesDefinition" );
				$novoXML_nodePD->setAttribute( "element", "ns2:vStorageProperties" );
				$novoXML_nodePD->setAttribute( "xmlns:ns2","http://www.example.com/tcc/udesc" );				
				$novoXML_NodeType->appendChild( $novoXML_nodePD );				
				$novoXML_Definitions->appendChild( $novoXML_NodeType );					
			}
			if($vRouterP){
				$novoXML_NodeType = $novoXML->createElement( "tosca:NodeType" );
				$novoXML_NodeType->setAttribute( "name", "TOSCAvRouterType" );				
				$novoXML_nodePD = $novoXML->createElement( "tosca:PropertiesDefinition" );
				$novoXML_nodePD->setAttribute( "element", "ns2:vRouterProperties" );
				$novoXML_nodePD->setAttribute( "xmlns:ns2","http://www.example.com/tcc/udesc" );				
				$novoXML_NodeType->appendChild( $novoXML_nodePD );				
				$novoXML_Definitions->appendChild( $novoXML_NodeType );		
			}			
			if ($relationshipP) {
				$novoXML_RelationshipType = $novoXML->createElement( "tosca:RelationshipType" );
				$novoXML_RelationshipType->setAttribute( "name", "TOSCAvLinkType" );
				$novoXML_relationshipPD = $novoXML->createElement( "tosca:PropertiesDefinition" );
				$novoXML_relationshipPD->setAttribute( "element", "ns3:vLinkProperties" );
				$novoXML_relationshipPD->setAttribute( "xmlns:ns3","http://www.example.com/tcc/udesc" );
				$novoXML_RelationshipType->appendChild( $novoXML_relationshipPD );
				$novoXML_Definitions->appendChild( $novoXML_RelationshipType );		
			}
			if($vAccessPointP){
				$novoXML_NodeType = $novoXML->createElement( "tosca:NodeType" );
				$novoXML_NodeType->setAttribute( "name", "TOSCAvAccessPointType" );				
				$novoXML_nodePD = $novoXML->createElement( "tosca:PropertiesDefinition" );
				$novoXML_nodePD->setAttribute( "element", "ns2:vAccessPointProperties" );
				$novoXML_nodePD->setAttribute( "xmlns:ns2","http://www.example.com/tcc/udesc" );				
				$novoXML_NodeType->appendChild( $novoXML_nodePD );				
				$novoXML_Definitions->appendChild( $novoXML_NodeType );
			}
			vaida_Conexoes($elementos);
			$novoNome = '../convertidos/TOSCA_'.$_REQUEST['arq']; 
			if($novoXML->save($novoNome)){
				$msg = conecta();
					if($msg != 'Conectado'){
					throw new RuntimeException("Erro ".$msg) ;
				}else{
					$nomeArq = 'TOSCA_'.$_REQUEST['arq'];
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
	    } else {
	        throw new RuntimeException('Erro ao abrir o arquivo\n');
	    }
	} catch (RuntimeException $e) {
		echo $e->getMessage();
	}
?>
