<?php

class Orion_View_Helper_FileUploader extends Zend_View_Helper_FormElement
{
    public function fileUploader($name, $attribs = null)
    {   
//        var_dump($this); die('test');
        $info = $this->_getInfo($name, null, $attribs);
       
        // XHTML or HTML end tag?
        $endTag = ' />';
        if (($this->view instanceof Zend_View_Abstract) && !$this->view->doctype()->isXhtml()) {
            $endTag= '>';
        }
        if(@$info['attribs'][0]){
            $diplay = 'block';
        } else {
            $diplay = 'none';
        }
        // build the element
        $xhtml = '
                    <div class="row fileupload-buttonbar">
                        <div class="span7">
                            <span id="'.$name.'"  class="btn btn-success fileinput-button">
                                <i class="icon-plus icon-white"></i>
                                <span>Dodaj zdjęcie...</span>
                            </span>
                        </div>
                        <input type="hidden" value="'.@$info['attribs'][0].'" name='.$name.' />
                        <div ><img  id="img_'.$name.'" style="display:'.$diplay.'; width:150px;" src="'.@$info['attribs'][0].'"/></div>
                    </div>
                ';
        $xhtml .='<script>
            $.ajax_upload("#'.$name.'", {
                action: "/profile/upload",                 
                name: "userfile",
                onComplete: function(file, response){
                    $("#img_'.$name.'").hide();
                    $("input[name='.$name.']").val(response);
                    $("#img_'.$name.'").attr("src",response);
                    $("#img_'.$name.'").css("width","150px");
                    $("#img_'.$name.'").show();
                },
                onSubmit: function(file, extension) {
                    $("#img_'.$name.'").css("width","36px");
                    $("#img_'.$name.'").attr("src","'.$this->view->imagePath("ajax-loader.gif",false,"global").'");
                    $("#img_'.$name.'").show();
                },
            });
            </script>';
        
        $this->view->headLink()->appendStylesheet($this->view->baseUrl() . '/skin/global/css/jquery.fileupload-ui.css'); 
        $this->view->headScript()->appendFile($this->view->baseUrl().'/skin/global/js/jquery.ajax_upload.0.6.js');   
        return $xhtml;
    }
}
