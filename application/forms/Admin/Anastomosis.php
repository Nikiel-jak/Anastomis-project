<?php
class App_Form_Admin_Anastomosis extends App_Form_Base
{
    private $row;



	private function grupNameMapping($name)
	{
		$names = array(
			'Hz3'							=> 'Izolacyjność dźwiękowa ważona',
			'Hz'							=> 'Izolacyjność dźwiękowa w oktawach i tercjach',
			'Waga pakietu 1m<sup>2</sup>'	=> 'Waga pakietu'
		);

		if (array_key_exists($name, $names))
		{
			return $names[$name];
		}
		
		return $name;
	}

    public function init()
    {
        $this->setName('Groups');
        $this->addAttribs(array('class' => 'form-horizontal'));

		$groups = array();

		$elementsp['name'] = new Zend_Form_Element_Text('name', array('belongsTo' => 'row'));
        $elementsp['name']->setRequired(true)
                 ->setLabel('FORMS_NAME')
                 ->setDecorators($this->textDecorator)->getDecorator('Label')->setRequiredSuffix(self::REQUIRED_FIELD);
		
		
		$elementsp['us_name'] = new Zend_Form_Element_Text('us_name', array('belongsTo' => 'row'));
        $elementsp['us_name']->setRequired(false)
                 ->setLabel('Nazwa US')
                 ->setDecorators($this->textDecorator)->getDecorator('Label')->setRequiredSuffix(self::REQUIRED_FIELD);				
			
        $elementsp['type'] = new Zend_Form_Element_Select('type', array('belongsTo' => 'row'));
        $elementsp['type']->setRequired(true)
                 ->setLabel('FORMS_TYPE')
                 ->setMultiOptions(App_Model_Anastomosis_DbTable::getType())
                 ->setDecorators($this->selectDecorator)->getDecorator('Label')->setRequiredSuffix(self::REQUIRED_FIELD);

        $pathEn = PUBLIC_PATH . '/upload/pdf/en/';

        $elementsp['en'] = new Zend_Form_Element_File('en', array('belongsTo' => 'row'));
        $elementsp['en']->setLabel('PDF - EN')
                 ->setDescription($this->row->pdf_en ? 'PDF - EN: <a target="_blank" href="/soundproof-glass-configurator/upload/pdf/en/'. $this->row->pdf_en.'">'.$this->row->pdf_en.'</a>' : 'Brak pliku')
                ->addValidator(new Zend_Validate_File_Extension('pdf'))
                ->addFilter(new Zend_Filter_File_Rename(['target' => $pathEn. '/' . rand(31, 40).time() .'.pdf', 'randomize'=> true]))
                 ->setDecorators($this->fileDecorator);

        $elementsp['en_remove'] = new Zend_Form_Element_Checkbox('en_remove', array('belongsTo' => 'row'));
        $elementsp['en_remove'] ->setLabel('Usuń pdf EN')
            ->setDecorators($this->textDecorator);

        $pathFr = PUBLIC_PATH . '/upload/pdf/fr/';

        $elementsp['fr'] = new Zend_Form_Element_File('fr', array('belongsTo' => 'row'));
        $elementsp['fr']->setLabel('PDF - FR')
                 ->setDescription($this->row->pdf_fr ? 'PDF - FR: <a target="_blank" href="/soundproof-glass-configurator/upload/pdf/fr/' . $this->row->pdf_fr.'">'.$this->row->pdf_fr.'</a>' : 'Brak pliku')
                ->addValidator(new Zend_Validate_File_Extension('pdf'))
                ->addFilter(new Zend_Filter_File_Rename(['target' => $pathFr. '/' . rand(21, 30).time() .'.pdf', 'randomize'=> true]))
                 ->setDecorators($this->fileDecorator);

        $elementsp['fr_remove'] = new Zend_Form_Element_Checkbox('fr_remove', array('belongsTo' => 'row'));
        $elementsp['fr_remove'] ->setLabel('Usuń pdf FR')
            ->setDecorators($this->textDecorator);

        $pathPl = PUBLIC_PATH . '/upload/pdf/pl/';

        $elementsp['pl'] = new Zend_Form_Element_File('pl', array('belongsTo' => 'row'));
        $elementsp['pl']->setLabel('PDF - PL')
                ->addValidator(new Zend_Validate_File_Extension('pdf'))
                ->addFilter(new Zend_Filter_File_Rename(['target' => $pathPl. '/' . rand(11, 20).time() .'.pdf', 'randomize'=> true]))
                 ->setDescription($this->row->pdf_pl ? 'PDF - PL: <a target="_blank" href="/soundproof-glass-configurator/upload/pdf/pl/' . $this->row->pdf_pl.'">'.$this->row->pdf_pl.'</a>' : 'Brak pliku')
                 ->setDecorators($this->fileDecorator);

        $elementsp['pl_remove'] = new Zend_Form_Element_Checkbox('pl_remove', array('belongsTo' => 'row'));
        $elementsp['pl_remove'] ->setLabel('Usuń pdf PL')
            ->setDecorators($this->textDecorator);

        $pathDe = PUBLIC_PATH . '/upload/pdf/de/';

        $elementsp['de'] = new Zend_Form_Element_File('de', array('belongsTo' => 'row'));
        $elementsp['de']->setDescription($this->row->pdf_de ? 'PDF - DE: <a  target="_blank" href="/soundproof-glass-configurator/upload/pdf/de/' . $this->row->pdf_de.'">'.$this->row->pdf_de.'</a>' : 'Brak pliku')
                 ->setLabel('PDF - DE')
                 ->addValidator(new Zend_Validate_File_Extension('pdf'))
                 ->addFilter(new Zend_Filter_File_Rename(['target' => $pathDe. '/' . rand(0, 10).time() .'.pdf', 'randomize'=> true]))
                 ->setDecorators($this->fileDecorator);

        $elementsp['de_remove'] = new Zend_Form_Element_Checkbox('de_remove', array('belongsTo' => 'row'));
        $elementsp['de_remove'] ->setLabel('Usuń pdf DE')
            ->setDecorators($this->textDecorator);

        $elementsp['status'] = new Zend_Form_Element_Checkbox('status', array('belongsTo' => 'row'));
        $elementsp['status'] ->setLabel('FORMS_STATUS_ACTIVE')
                 ->setDecorators($this->textDecorator);

	    $elementsp['show_in_us'] = new Zend_Form_Element_Checkbox('show_in_us', array('belongsTo' => 'row'));
        $elementsp['show_in_us'] ->setLabel('Dane dla wersji Amerykańskiej: ')
            ->setChecked(true)
            ->setDecorators($this->textDecorator);


		$this->addElements($elementsp);

		$groups['Dane podstawowe'][] = 'name';
		$groups['Dane podstawowe'][] = 'us_name';
		$groups['Dane podstawowe'][] = 'type';
		$groups['Dane podstawowe'][] = 'show_in_us';
		$groups['Dane podstawowe'][] = 'status';
		$groups['Dane podstawowe'][] = 'en';
		$groups['Dane podstawowe'][] = 'en_remove';
		$groups['Dane podstawowe'][] = 'fr';
		$groups['Dane podstawowe'][] = 'fr_remove';
		$groups['Dane podstawowe'][] = 'pl';
		$groups['Dane podstawowe'][] = 'pl_remove';
		$groups['Dane podstawowe'][] = 'de';
		$groups['Dane podstawowe'][] = 'de_remove';





        $model = new App_Model_Attributes_DbTable();
        $results = $model->getGroupWithAttributesToForm();
        
       
        if(count($results)){
            foreach($results as $row){
				if ($row->type == App_Model_Anastomosis_DbTable::TYPE_INPUT_SELECT){
                    $options=[];
                   for($i=$row->min; $i<=$row->max;  $i+=$row->step){
                        $options[$i]=$i;
                    }
                    $elements[$row->id] = new Zend_Form_Element_Select($row->id, array('belongsTo' => 'row'));
                    $elements[$row->id]->setDecorators($this->selectDecorator)
                        ->setMultiOptions($options)
                        ->setLabel(ucfirst($row->name).':')
                        ->setAttrib('class', 'span1');

                }else{
                    $elements[$row->id] = new Zend_Form_Element_Text($row->id, array('belongsTo' => 'row'));
                    $elements[$row->id]->setDecorators($this->textDecorator)
                        ->setLabel(ucfirst($row->name).':')
                        ->setAttrib('class', 'span1');
                }
                if($row->not_required == 0){
                    $elements[$row->id]->setRequired(true)->getDecorator('Label')->setRequiredSuffix(self::REQUIRED_FIELD);
                }
                $groups[$this->grupNameMapping($row->group_name)][] = $row->id;
            }
        }
        
        
        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setIgnore(true)
                ->setAttrib('class','btn')
               ->setDecorators($this->submitDecorator)
               ->setLabel('FORMS_SAVE');
               
	
        $this->addElements($elements);
        
        if(count($groups)){
            foreach($groups as $key => $group){
                $this->addDisplayGroup($group,$key,array('legend' => $key));
                $senderlist = $this->getDisplayGroup($key);
                $senderlist->setDecorators($this->fieldsetDecorator);
            }
        }

        $this->addElement($submit);
    }

    public function __construct($row = null, $options = null)
    {
        $this->row = $row;
        parent::__construct($options);
    }
}