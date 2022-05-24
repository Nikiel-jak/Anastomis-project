<?php

class Orion_ListView
{
    // Nazwa listy! Musi być unikalna dla różnych list
    protected $_name;
    protected $_privateSessionName;

    protected $_select;
    
    // If 0, no pagination occurs
    protected $_itemsPerPage = 10;
    
    // Fileds shown
    protected $_columns;
    
    // Invisible columns
    protected $_invisibleColumns;
    
    // Filters
    protected $_filters;
    
    // Sortable Columns
    protected $_sortableColumns;

    // setDefaultSortForList
    protected $_defaultSort;

    // Authomatic generation of filter form
    protected $_autoFilterForm = true;
    
    // Column which should be grouped
    protected $_groupBy;
    
    // ASC|DESC
    protected $_orderType;
    
    // column with textalign formatting
    protected $_textAlign;
    
    // Formatted URL attachmed to rowclick
    protected $_onClickURL;
    
    // Buttons below in form array ( array (label => ..., url => ..., icon => ...), ... )

    // Buttons
    protected $_buttons;
    
    // Buttons above the table (in the right)
    protected $_topButtons;
    
    // Number column
    protected $_numberColumn;
    
    // Default value for empty strings and NULL values
    protected $_emptyValue = '---';
    
    // Paginator
    protected $_paginator;
    // Session
    protected $_session;
    // forms use
    protected $_forms;
    //option to change items per page
    protected $_itemsPerPageOptions;
    //search in fields (array)
    protected $_search;

    protected $_searchValue;

    protected $_useForms = true;

    protected $_text;

    protected $_header = true;

    public function __construct($name, $config)
    {
        if(is_null($name))
        {
            throw new Exception('Incorrect ListView name!');
        }
        $this->_name = $name;
        
        if (!isset($config['select']) || !($config['select'] instanceof Zend_Db_Table_Select))
        {
            throw new Exception('Incorrect select!');
        }
        $this->_select = $config['select'];
        
        if(!isset($config['columns']) || count($config['columns']) == 0)
        {
            throw new Exception('Incorrect columns config!');
        }
        $this->_columns = $config['columns'];
        
        // PÓKI CO BRAK WERYFIKACJI POPRAWNOŚCI DANYCH WEJŚCIOWYCH, PROSZĘ WPROWADZAĆ POPRAWNE :)
        
        if(isset($config['itemsPerPage'])) $this->_itemsPerPage = $config['itemsPerPage'];
        if(isset($config['filters'])) $this->_filters = $config['filters']; else $this->_filters = array();
        if(isset($config['sortableColumns'])) $this->_sortableColumns = $config['sortableColumns']; else $this->_sortableColumns = array();
        if(isset($config['autoFilterForm'])) $this->_autoFilterForm = $config['autoFilterForm'];
        if(isset($config['groupBy'])) $this->_groupBy = $config['groupBy'];
        if(isset($config['orderType'])) $this->_orderType = $config['orderType'];
        if(isset($config['textAlign'])) $this->_textAlign = $config['textAlign'];
        if(isset($config['onClickURL'])) $this->_onClickURL = $config['onClickURL'];
        if(isset($config['buttons'])) $this->_buttons = $config['buttons']; else $this->_buttons = array();
        //if(isset($config['specialColumns'])) $this->_specialColumns = $config['specialColumns']; else $this->_specialColumns = array();
        if(isset($config['topButtons'])) $this->_topButtons = $config['topButtons']; else $this->_topButtons = array();
        if(isset($config['numberColumn'])) $this->_numberColumn = $config['numberColumn'];
        if(isset($config['invisibleColumns'])) $this->_invisibleColumns = $config['invisibleColumns']; else $this->_invisibleColumns = array();
        if(isset($config['emptyValue'])) $this->_emptyValue = $config['emptyValue'];
        if(isset($config['privateSessionName'])) $this->_privateSessionName = $config['privateSessionName'];
        if(isset($config['defaultSort'])) $this->_defaultSort = $config['defaultSort'];
        if(isset($config['forms'])) $this->_forms = $config['forms'];
        if(isset($config['itemsPerPageOptions'])) $this->_itemsPerPageOptions = $config['itemsPerPageOptions'];
        if(isset($config['search'])) $this->_search = $config['search'];
        if(isset($config['useForms'])) $this->_useForms = $config['useForms'];
        if(isset($config['text'])) $this->_text = $config['text'];
        if(isset($config['header'])) $this->_header = $config['header'];
    }
    
    public function init($postdata)
    {
        // Jeśli nowa sesja, to uzupełnij domyślnymi danymi
        $session_name = (isset($this->_privateSessionName)) ? $this->_privateSessionName : $this->_name;
        $session = new Zend_Session_Namespace($session_name);

        if(!isset($session->filter))
        { 
            $session->filter = array();
            
            // Sprawdzamy czy filtry nie mają domyślnych wartości
            foreach($this->_filters as $filter)
            {
                $filter_name = $filter->getName();
                $defaultData = $filter->getDefaultSessionData();
                if($defaultData != null)
                {
                    $session->filter[$filter_name] = $defaultData;
                }
            }
        }

        if(!isset($session->paginator)) $session->page = 1;

        if(!isset($session->sort)) {
            $session->sort = $this->_defaultSort ? $this->_defaultSort : array();
        }
        /* Uaktualniamy SESJĘ na podstawie POST!!! */
        if(count($postdata) != 0)
        {
            // Każdy POST z sortowaniem, filtracją, czy paginacją musi mieć pole __listview__ z nazwą listview
            if(!isset($postdata['__listview__'])) return;
            
            if($postdata['__listview__'] == $this->_name)
            {
                // Najpierw filtracja
                if(isset($postdata['filter']))
                {
                    $session->filter = array();

                    foreach($this->_filters as $filter)
                    {
                        $filter_name = $filter->getName();

                        // Przetwórz dane z POST i uaktualnij sesję
                        if(isset($postdata['filter'][$filter_name]))
                            $session->filter[$filter_name] = $filter->getSessionData($postdata['filter'][$filter_name]);
                    }

                    // po filtracji odswieżamy zawsze stronę na 1
                    $session->paginator = 1;

                }

                // Sortowanie
                if(isset($postdata['sort']))
                {
                    if(in_array($postdata['sort'], $this->_sortableColumns) || isset($this->_sortableColumns[$postdata['sort']]))
                    {
                        //$session->sort[0] = $postdata['sort'];
                        if(isset($session->sort[0]))
                        {
                            if($session->sort[0] == $postdata['sort'])
                            {
                                $session->sort[1] = !$session->sort[1];
                            }
                            else
                            {
                                $session->sort[0] = $postdata['sort'];
                                $session->sort[1] = true;
                            }
                        }
                        else
                        {
                            $session->sort[0] = $postdata['sort'];
                            $session->sort[1] = true;
                        }
                    }
                    // po sortowaniu odswieżamy zawsze stronę na 1
                    $session->paginator = 1;
                }

                // Paginacja
                if(isset($postdata['paginator']))
                {
                    $session->paginator = $postdata['paginator'];
                }

                //items per page
                if(isset($postdata['itemsperpage'])){
                    $session->itemsperpage = $postdata['itemsperpage'];
                }

                //search
                if(isset($postdata['search'])){
                    $session->search = $postdata['search'];
                }
            }
        }


        /* Generujemy zapytanie */

        $select = $this->_select;
        // Filtry
        foreach($this->_filters as $filter)
        {
            $filter_name = $filter->getName();
                                
            // Ustaw listview, do którego należy filtr
            $filter->setListView($this);
            
            // Ustaw aktualne dane sesji do filtra
            if(isset($session->filter[$filter_name]))
                $filter->setSessionData($session->filter[$filter_name]);

            // Ustaw status
            $filter->setStatus();
            
            if($filter->getStatus() == Orion_ListView_Filter::FILTER_READY)
            {
                $filter->addWhere($select);
            }
        }
        // SeARCH
        if(isset($session->search) && !empty($session->search)){
            $this->_searchValue = $session->search;
            $columns = $this->getSearch();
            $total = count($columns);
            foreach($columns as $key => $column){
                if($key == 0 && $key+1 != $total ){
                    $select->where("({$column} LIKE ?",'%'.$session->search.'%');
                } elseif($key+1 == $total && $key != 0){
                    $select->orWhere("{$column} LIKE ?)",'%'.$session->search.'%');
                } elseif($key != 0) {
                    $select->orWhere("{$column} LIKE ?",'%'.$session->search.'%');
                } else {
                    $select->Where("{$column} LIKE ?",'%'.$session->search.'%');
                }
            }
        }

        // Sortowanie
        if(count($session->sort) != 0)
        {
            $order_type = $session->sort[1] ? 'ASC' : 'DESC';
            
            if(isset($this->_sortableColumns[$session->sort[0]])) {
                $order = $this->_sortableColumns[$session->sort[0]];
            }else {
                $order = $this->_columns[$session->sort[0]][0];
            }
            
            $select->order("{$order} {$order_type}");
        }
        else
        {
            if(isset($this->_groupBy))
            {
                if(isset($this->_orderType) && in_array($this->_orderType, array('ASC', 'asc', 'DESC', 'desc'))) 
                    $o_type = $this->_orderType;
                else $o_type = "ASC";
                $select->order("{$this->_groupBy[0]} $o_type");
                $session->sort[0] = $this->_groupBy[0];
                $session->sort[1] = ($o_type == 'ASC' || $o_type == 'asc') ? true : false;
                /* Tu jest taka rzecz, że _orderType ustawia domyślny typ sortowania na głównej grupie
                 * Można w taki sposób wprowadzić domyślne sortowanie (co do daty) za pomocą mechanizmu grupowania,
                 * Powinien to robić  oddzielny parametr getDefaultSort, ale poki co nie ma na to czasu
                 * Wiec to jest na przyszlosc do zmiany, ale jako tako dziala
                 * 
                 */
                
                //$select->order("{$this->_groupBy[0]} ASC");
            }
        }

        //Items per page
        if(isset($session) && isset($session->itemsperpage)){
            $this->_itemsPerPage = $session->itemsperpage;
        }
        $this->_paginator = Zend_Paginator::factory($select);
        if($this->_itemsPerPage == 0) {
            $this->_paginator->setItemCountPerPage(1000000000);
        } else {
            $this->_paginator->setItemCountPerPage($this->_itemsPerPage);
        }
        $this->_paginator->setCurrentPageNumber($session->paginator);
        
        $this->_session = $session;
    }
    
    public function build()
    {
        $view = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('view');
        
        $rowspan_table = array();
        $last_key = 0;
        
        if(isset($this->_groupBy))
        {
            foreach($this->_paginator as $key => $value)
            {
                if(count($rowspan_table) == 0)
                {
                    $rowspan_table[$key] = array($value->{$this->_groupBy[0]}, 1);
                }
                else if($rowspan_table[$last_key][0] == $value->{$this->_groupBy[0]})
                {
                    $rowspan_table[$last_key][1] =  $rowspan_table[$last_key][1] + 1;
                }
                else
                {
                    $last_key++;
                    $rowspan_table[$last_key] = array($value->{$this->_groupBy[0]}, 1);
                }
            }
        }
        
        //var_dump($rowspan_table);die();

        return $view->partial('helpers/partials/list_view.phtml', array(            
            'name' => $this->_name,
            'columns' => $this->_columns,
            'filters' => $this->_filters,
            'sortableColumns' => $this->_sortableColumns,
            'pagesPerPage' => $this->_itemsPerPage,
            'paginator' => $this->_paginator,
            'autoFilterForm' => $this->_autoFilterForm,
            'session' => $this->_session,
            'buttons' => $this->_buttons,
            'onClickURL' => $this->_onClickURL,
            'topButtons' => $this->_topButtons,
            'groupBy' => $this->_groupBy,
            'orderType' => $this->_orderType,
            'numberColumn' => $this->_numberColumn,
            'invisibleColumns' => $this->_invisibleColumns,
            'emptyValue' => $this->_emptyValue, 
            'rowspan' => $rowspan_table,
            'listview' => $this,
            'forms' => $this->_forms,
            'itemsPerPageOptions' => $this->_itemsPerPageOptions,
            'minItemsPerPage' => $this->getMinItemsPerPage(),
            'search' => (isset($this->_search)) ? true : false,
            'searchValue' => $this->_searchValue,
            'useForms' => $this->_useForms,
            'text' => $this->_text,
            'header' => $this->_header
        ));
    }

    public function getMinItemsPerPage()
    {
        if(count($this->_itemsPerPageOptions)){
            return $this->_itemsPerPageOptions[0];
        }
        return false;
    }
    
    public function enablePagination($pages)
    {
        $this->_itemsPerPage = $pages;
    }
    
    public function disablePagination()
    {
        $this->_itemsPerPage = 0;
    }
    
    public function transformArray($arr, $row)
    {
        foreach($arr as $key => $val)
        {
            $arr[$key] = self::transformButton($val, $row);
        }
        return $arr;
    }
    
    public function transformButton($str, $row)
    {
        foreach($this->_columns as $colname => $parameters)
        {
            if($parameters[0] == '__special__') continue;
            $str = preg_replace("/\{{$colname}\}/", $row->$colname, $str);
        }
        
        foreach($this->_invisibleColumns as $colname => $parameters)
        {
            $str = preg_replace("/\{{$colname}\}/", $row->$colname, $str);
        }
        return $str;
    }
    
    public function getColumnNamePairs()
    {
        $result = array();
        foreach($this->_columns as $key => $val)
        {
            if($val[0] == '__special__') continue;
            $result[$key] = $val[0];
        }
        foreach($this->_invisibleColumns as $key => $val)
        {
            $result[$key] = $val[0];
        }
        return $result;
    }
    
    public function getName()
    {
        if($this->_text){
            return $this->_text;
        }
        return $this->_name;
    }
    
    public function getEmptyValue()
    {
        return $this->_emptyValue;
    }

    public function getSearch()
    {
        return $this->_search;
    }

    public function getPaginator()
    {
        return $this->_paginator;
    }
}