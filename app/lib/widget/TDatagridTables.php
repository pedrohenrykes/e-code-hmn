<?php

class TDatagridTables extends TTable {

    protected $columns;
    protected $actions;
    protected $action_groups;
    protected $rowcount;
    protected $tbody;
    protected $height;
    protected $scrollable;
    protected $modelCreated;
    protected $pageNavigation;
    protected $defaultClick;
    protected $groupColumn;
    protected $groupContent;
    protected $groupMask;
    protected $popover;
    protected $poptitle;
    protected $popcontent;
    protected $objects;
    protected $exportBtn;

    /**
     * Class Constructor
     */
    public function __construct($exportBtn = false) {
        parent::__construct();
        $this->modelCreated = FALSE;
        $this->defaultClick = TRUE;
        $this->popover = FALSE;
        $this->groupColumn = NULL;
        $this->groupContent = NULL;
        $this->groupMask = NULL;
        $this->actions = array();
        $this->action_groups = array();
        //$this->{'class'} = 'display nowrap';
        $this->{'class'} = 'display responsive nowrap';
        $this->{'id'} = 'example';
        $this->cellspacing = '0';
        $this->width = '100%';
        $this->exportBtn = $exportBtn;
    }

    /**
     * Enable popover
     * @param $title Title
     * @param $content Content
     */
    public function enablePopover($title, $content) {
        $this->popover = TRUE;
        $this->poptitle = $title;
        $this->popcontent = $content;
    }

    /**
     * Make the datagrid scrollable
     */
    public function makeScrollable() {
        $this->scrollable = TRUE;
    }

    /**
     * disable the default click action
     */
    public function disableDefaultClick() {
        $this->defaultClick = FALSE;
    }

    /**
     * Define the Height
     * @param $height An integer containing the height
     */
    function setHeight($height) {
        $this->height = $height;
    }

    /**
     * Add a Column to the DataGrid
     * @param $object A TDataGridColumn object
     */
    public function addColumn(TDataGridColumn $object) {
        if ($this->modelCreated) {
            throw new Exception(AdiantiCoreTranslator::translate('You must call ^1 before ^2', __METHOD__, 'createModel'));
        } else {
            $this->columns[] = $object;
        }
    }

    /**
     * Add an Action to the DataGrid
     * @param $object A TDataGridAction object
     */
    public function addAction(TDataGridAction $object) {
        if (!$object->getField()) {
            throw new Exception(AdiantiCoreTranslator::translate('You must define the field for the action (^1)', $object->toString()));
        }

        if ($this->modelCreated) {
            throw new Exception(AdiantiCoreTranslator::translate('You must call ^1 before ^2', __METHOD__, 'createModel'));
        } else {
            $this->actions[] = $object;
        }
    }

    /**
     * Add an Action Group to the DataGrid
     * @param $object A TDataGridActionGroup object
     */
    public function addActionGroup(TDataGridActionGroup $object) {
        if ($this->modelCreated) {
            throw new Exception(AdiantiCoreTranslator::translate('You must call ^1 before ^2', __METHOD__, 'createModel'));
        } else {
            $this->action_groups[] = $object;
        }
    }

    public function setGroupColumn($column, $mask) {
        $this->groupColumn = $column;
        $this->groupMask = $mask;
    }

    /**
     * Clear the DataGrid contents
     */
    function clear() {
        if ($this->modelCreated) {
            // copy the headers
            $copy = $this->children[0];
            // reset the row array
            $this->children = array();
            // add the header again
            $this->children[] = $copy;

            // add an empty body
            $this->tbody = new TElement('tbody');
            //     $this->tbody->{'class'} = 'tdatagrid_body';
//            if ($this->scrollable) {
//                $this->tbody->{'style'} = "height: {$this->height}px; display: block; overflow-y:scroll; overflow-x:hidden;";
//            }
            parent::add($this->tbody);

            // restart the row count
            $this->rowcount = 0;
        }
    }

    /**
     * Creates the DataGrid Structure
     */
    public function createModel() {
        if (!$this->columns) {
            return;
        }

        $thead = new TElement('thead');
        // $thead->{'class'} = 'tdatagrid_head';
        parent::add($thead);

        $row = new TElement('tr');
//        if ($this->scrollable) {
//            $row->{'style'} = 'display:block';
//        }
        $thead->add($row);

        $actions_count = count($this->actions) + count($this->action_groups);

        if ($actions_count > 0) {
            for ($n = 0; $n < $actions_count; $n++) {
                $cell = new TElement('th');
                $row->add($cell);
                //  $cell->add('&nbsp;');
                // $cell->{'class'} = 'tdatagrid_action';
                // $cell->width = '16px';
            }

            //  $cell->{'class'} = 'tdatagrid_col';
        }

        // add some cells for the data
        if ($this->columns) {
            // iterate the DataGrid columns
            foreach ($this->columns as $column) {
                // get the column properties
                $name = $column->getName();
                //$label = '&nbsp;' . $column->getLabel() . '&nbsp;';
                $label = $column->getLabel();
                //     $align = $column->getAlign();
                $width = $column->getWidth();
//                if (isset($_GET['order'])) {
//                    if ($_GET['order'] == $name) {
//                        $label .= '<img src="lib/adianti/images/ico_down.png">';
//                    }
//                }
                // add a cell with the columns label
                $cell = new TElement('th');
                $row->add($cell);
                $cell->add($label);

                //  $cell->{'class'} = 'tdatagrid_col';
                //      $cell->align = $align;
//                if ($width) {
//                    $cell->width = ($width + 8) . 'px';
//                }
                // verify if the column has an attached action
                if ($column->getAction()) {
                    $url = $column->getAction();
                    $cell->href = $url;
                    $cell->generator = 'adianti';
                }
            }

            if ($this->scrollable) {
                $cell = new TElement('td');
                //     $cell->{'class'} = 'tdatagrid_col';
                $row->add($cell);
                //   $cell->add('&nbsp;');
                // $cell->width = '12px';
            }
        }

        // add one row to the DataGrid
        $this->tbody = new TElement('tbody');
        //   $this->tbody->{'class'} = 'tdatagrid_body';
//        if ($this->scrollable) {
//            $this->tbody->{'style'} = "height: {$this->height}px; display: block; overflow-y:scroll; overflow-x:hidden;";
//        }
        parent::add($this->tbody);

        $this->modelCreated = TRUE;
    }

    /**
     * insert content
     */
    public function insert($position, $content) {
        $this->tbody->insert($position, $content);
    }

    /**
     * Add an object to the DataGrid
     * @param $object An Active Record Object
     */
    public function addItem($object) {
        if ($this->modelCreated) {
            if ($this->groupColumn AND ( is_null($this->groupContent) OR $this->groupContent !== $object->{$this->groupColumn} )) {
                $row = new TElement('tr');
                $row->{'class'} = 'tdatagrid_group';
                $this->tbody->add($row);
                $cell = new TElement('td');
                $cell->add($this->replace($this->groupMask, $object));
                $cell->colspan = count($this->actions) + count($this->action_groups) + count($this->columns);
                $row->add($cell);
                $this->groupContent = $object->{$this->groupColumn};
            }

            // define the background color for that line
            // $classname = ($this->rowcount % 2) == 0 ? 'tdatagrid_row_even' : 'tdatagrid_row_odd';

            $row = new TElement('tr');
            $this->tbody->add($row);
            //  $row->{'class'} = $classname;

            if ($this->actions) {
                // iterate the actions
                foreach ($this->actions as $action) {
                    $this->prepareAction($action, $object); // validate action
                    // get the action properties
                    $label = $action->getLabel();
                    $image = $action->getImage();
                    $condition = $action->getDisplayCondition();

                    if (empty($condition) OR call_user_func($condition, $object)) {
                        $url = $action->serialize();
                        $first_url = isset($first_url) ? $first_url : $url;

                        // creates a link
                        $link = new TElement('a');
                        $link->href = $url;
                        $link->generator = 'adianti';

                        // verify if the link will have an icon or a label
                        if ($image) {
                            $image_tag = new TImage($image);
                            $image_tag->title = $label;
                            $link->add($image_tag);
                        } else {
                            // add the label to the link
                            $span = new TElement('span');
                            $span->{'class'} = 'btn btn-default';
                            $span->add($label);
                            $link->add($span);
                        }
                    } else {
                        $link = '';
                    }

                    // add the cell to the row
                    $cell = new TElement('td');
                    $row->add($cell);
                    $cell->add($link);
                    //    $cell->width = '16px';
                    //  $cell->{'class'} = 'tdatagrid_cell action';
                }
            }

            if ($this->action_groups) {
                foreach ($this->action_groups as $action_group) {
                    $actions = $action_group->getActions();
                    $headers = $action_group->getHeaders();
                    $separators = $action_group->getSeparators();

                    if ($actions) {
                        $dropdown = new TDropDown($action_group->getLabel(), $action_group->getIcon());
                        $last_index = 0;
                        foreach ($actions as $index => $action) {
                            // add intermediate headers and separators
                            for ($n = $last_index; $n < $index; $n++) {
                                if (isset($headers[$n])) {
                                    $dropdown->addHeader($headers[$n]);
                                }
                                if (isset($separators[$n])) {
                                    $dropdown->addSeparator();
                                }
                            }
                            // get the action properties
                            $label = $action->getLabel();
                            $image = $action->getImage();
                            $condition = $action->getDisplayCondition();
                            if (empty($condition) OR call_user_func($condition, $object)) {
                                $this->prepareAction($action, $object); // validate action
                                $url = $action->serialize();
                                $first_url = isset($first_url) ? $first_url : $url;
                                $dropdown->addAction($label, $action, $image);
                            }
                            $last_index = $index;
                        }
                        // add the cell to the row
                        $cell = new TElement('td');
                        $row->add($cell);
                        $cell->add($dropdown);
                        //   $cell->{'class'} = 'tdatagrid_cell action';
                    }
                }
            }

            if ($this->columns) {
                // iterate the DataGrid columns
                foreach ($this->columns as $column) {
                    // get the column properties
                    $name = $column->getName();
                    $align = $column->getAlign();
                    $width = $column->getWidth();
                    $function = $column->getTransformer();
                    $content = $object->$name;
                    $data = is_null($content) ? '' : $content;
                    // verify if there's a transformer function
                    if ($function) {
                        // apply the transformer functions over the data
                        $data = call_user_func($function, $data, $object, $row);
                    }

                    if ($editaction = $column->getEditAction()) {
                        $editaction_field = $editaction->getField();
                        $div = new TElement('div');
                        $div->{'class'} = 'inlineediting';
                        $div->{'style'} = 'padding-left:5px;padding-right:5px';
                        $div->{'action'} = $editaction->serialize();
                        $div->{'field'} = $name;
                        $div->{'key'} = isset($object->{$editaction_field}) ? $object->{$editaction_field} : NULL;
                        $div->add($data);
                        $cell = new TElement('td');
                        $row->add($cell);
                        $cell->add($div);
                        //  $cell->{'class'} = 'tdatagrid_cell';
                    } else {
                        // add the cell to the row
                        $cell = new TElement('td');
                        $row->add($cell);
                        $cell->add($data);
                        //  $cell->{'class'} = 'tdatagrid_cell';
                        $cell->align = $align;

                        if (isset($first_url) AND $this->defaultClick) {
                            $cell->href = $first_url;
                            $cell->generator = 'adianti';
                            //   $cell->{'class'} = 'tdatagrid_cell';
                        }
                    }
                    if ($width) {
                        $cell->width = $width . 'px';
                    }
                }
            }

            if ($this->popover) {
                $data = method_exists($object, 'toArray') ? $object->toArray() : (array) $object;
                $poptitle = $this->poptitle;
                $popcontent = $this->popcontent;
                foreach ($data as $property => $value) {
                    $poptitle = str_replace('{' . $property . '}', $value, $poptitle);
                    $popcontent = str_replace('{' . $property . '}', $value, $popcontent);
                }

                $row->popover = 'true';
                $row->poptitle = $poptitle;
                $row->popcontent = $popcontent;
            }

            $this->objects[$this->rowcount] = $object;

            // increments the row counter
            $this->rowcount ++;

            return $row;
        } else {
            throw new Exception(AdiantiCoreTranslator::translate('You must call ^1 before ^2', 'createModel', __METHOD__));
        }
    }

    /**
     * Replace a string with object properties within {pattern}
     * @param $content String with pattern
     * @param $object  Any object
     */
    private function replace($content, $object) {
        if (preg_match_all('/\{(.*?)\}/', $content, $matches)) {
            foreach ($matches[0] as $match) {
                $property = substr($match, 1, -1);
                $content = str_replace($match, $object->$property, $content);
            }
        }

        return $content;
    }

    /**
     * Find the row index by object attribute
     * @param $attribute Object attribute
     * @param $value Object value
     */
    public function getRowIndex($attribute, $value) {
        foreach ($this->objects as $pos => $object) {
            if ($object->$attribute == $value) {

                return $pos;
            }
        }
        return NULL;
    }

    /**
     * Return the row by position
     * @param $position Row position
     */
    public function getRow($position) {
        return $this->tbody->get($position);
    }

    /**
     * Prepare action for use
     * @param $action TAction
     * @param $object Data Object
     */
    private function prepareAction(TAction $action, $object) {
        $field = $action->getField();

        if (is_null($field)) {
            throw new Exception(AdiantiCoreTranslator::translate('Field for action ^1 not defined', $label) . '.<br>' .
            AdiantiCoreTranslator::translate('Use the ^1 method', 'setField' . '()') . '.');
        }

        if (!isset($object->$field)) {
            throw new Exception(AdiantiCoreTranslator::translate('Field ^1 not exists', $field));
        }

        // get the object property that will be passed ahead
        $key = isset($object->$field) ? $object->$field : NULL;
        $action->setParameter('key', $key);

        $fieldfk = $action->getFk();
        if (isset($fieldfk)) {
            if (!isset($object->$fieldfk)) {
                throw new Exception(AdiantiCoreTranslator::translate('FK ^1 not exists', $field));
            }
            $fk = isset($object->$fieldfk) ? $object->$fieldfk : NULL;
            $action->setParameter('fk', $fk);
        }

    }

    /**
     * Returns the DataGrid's width
     * @return An integer containing the DataGrid's width
     */
    public function getWidth() {
        $width = 0;
        if ($this->actions) {
            // iterate the DataGrid Actions
            foreach ($this->actions as $action) {
                $width += 22;
            }
        }

        if ($this->columns) {
            // iterate the DataGrid Columns
            foreach ($this->columns as $column) {
                $width += $column->getWidth();
            }
        }
        return $width;
    }

    /**
     * Shows the DataGrid
     */
    function show() {
        // shows the datagrid
        parent::show();

        $params = $_REQUEST;
        unset($params['class']);
        unset($params['method']);
        // to keep browsing parameters (order, page, first_page, ...)
        $urlparams = '&' . http_build_query($params);

        // inline editing treatment
        TScript::create('$(function() {
        	$(".inlineediting").editInPlace({
        		callback: function(unused, enteredText)
        		{
        		    __adianti_load_page($(this).attr("action")+"' . $urlparams . '&key="+$(this).attr("key")+"&field="+$(this).attr("field")+"&value="+encodeURIComponent(enteredText));
        		    return enteredText;
        		},
        		show_buttons: false,
        		text_size:20,
        		params:column=name
    	    });
        });');
        //inicializar o dataTables na table id=example
        if ($this->exportBtn == FALSE) {
            TPage::include_js('app/lib/include/initDataTable.js');
        } else {
            TScript::create('
                    $(document).ready(function() {
                         $("#example").dataTable();
                      } );');
        }
    }

    /**
     * Assign a PageNavigation object
     * @param $pageNavigation object
     */
    function setPageNavigation($pageNavigation) {
        $this->pageNavigation = $pageNavigation;
    }

    /**
     * Return the assigned PageNavigation object
     * @return $pageNavigation object
     */
    function getPageNavigation() {
        return $this->pageNavigation;
    }

}
