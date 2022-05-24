<?php

abstract class Orion_ListView_Filter
{
    protected $_name;
    protected $_listview;
    protected $_sessionData;
    
    const FILTER_EMPTY = 1;
    const FILTER_INCORRECT = 2;
    const FILTER_READY = 3;
    
    protected $_status;
    
    public function __construct($name)
    {
        $this->_name = $name;
        $this->_status = self::FILTER_EMPTY;
    }
    
    public function setListView($listview)
    {
        $this->_listview = $listview;
    }
    
    public function setSessionData($session_data)
    {
        $this->_sessionData = $session_data;
    }
    
    public function getName()
    {
        return $this->_name;
    }
    
    public function getStatus()
    {
        return $this->_status;
    }

    
    /*
     * Ta metoda przygotowuje dane do sesji, które będą trzymane dla tego filtru na podstawie tego co przyjdzie z formularza
     */
    abstract public function getSessionData($post);
    
    /*
     * Ta metoda w zależności od danych sesji ustawia status filtru na FILTER_EMPTY, FILTER_READY lub FILTER_INCORRECT
     */
    abstract public function setStatus();
    
    // wyswietlenie filtrów
    abstract public function render();
    
    // dodanie whereów
    abstract public function addWhere(&$select);
    
    // pobranie domyślnej wartości która ma być trzymana w sesji
    // przy inicjalizacji sesji dla danego listview raz jest ustawiana i potem filtr działa normalnie jak filtr
    public function getDefaultSessionData()
    {
        return null;
    }
}

?>
