<?php

namespace App\Http\Controllers\smartEnvios;

use App\Http\Controllers\Controller;
use CURLFile;
use Illuminate\Http\Request;
use SimpleXMLElement;

class requestUploadController extends Controller
{
    public function RequestUpload(Request $request)
    {
         if ($request->hasFile('file')) {

            $file = $request->file('file');

            // Criar um novo objeto SimpleXMLElement ou carregar um XML existente
            $xml = new SimpleXMLElement(file_get_contents($file->path()));

            // Adicionar os elementos da NF-e aqui

            // Adicionar o bloco <vol> com os dados específicos
            if (isset($xml->NFe->infNFe->transp)) {
                // Adicionar o bloco <vol> dentro de <transp>
                $volElement = $xml->NFe->infNFe->transp->addChild('vol');
                $volElement->addChild('qVol', $this->QuantityVolume($request->peso));
                $volElement->addChild('esp', 'CAIXA');
                $volElement->addChild('marca', 'Embaleme');
                $volElement->addChild('nVol', $this->QuantityVolume($request->peso));
                $volElement->addChild('pesoL', $this->convertPeso($request->peso));
                $volElement->addChild('pesoB',$this->convertPeso($request->peso));
                // Gerar XML final sem a declaração XML no cabeçalho
                $xmlString = preg_replace("/<\\?xml.*\\?>/",'',$xml->saveXML(),1);
            }

        $tempFile = tempnam(sys_get_temp_dir(), 'xml_file');
        file_put_contents($tempFile, $xmlString);

        $upload = new
        requestSmartEnviosController("a66cb425-a04c-460a-a0ac-b5ef61367e50",$request->freight_order_id,$tempFile,"jsVC2QAsoHijI0ULb7hkyku9kq8117nw");
        $status = $upload->resource();
        (new etiquetaController($upload))->resource();

        if($status == "200"){
            return redirect()->back()->with('msg',"Xml Enviada Com Sucesso!");
        }else{
             $data = json_decode(json_encode($status));
            return redirect()->back()->with('error',$data->message);
        }

    }
}

public function convertPeso($peso){

    switch (strlen($peso)) {
        case 1:
            return $peso / 1;
            case 2:
                return $peso / 10;
                case 3:
                    return $peso / 100;
                    case 4:
                        return $peso / 1000;
                        case 5:
                            return $peso / 1000;
                        }
    }

    public function QuantityVolume($peso){
        if($peso <= 30000){
            return 1;
        }else{
        $volumes = $peso / 30000;
        return ceil($volumes);
        }
    }
}
