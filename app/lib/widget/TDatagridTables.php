<?php

class TDatagridTables extends TTable
{
    protected $columns;
    protected $actions;
    protected $action_groups;
    protected $rowcount;
    protected $thead;
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
    protected $actionWidth;
    protected $groupCount;
    protected $groupRowCount;
    protected $columnValues;

    public function __construct($exportBtn = false)
    {
        parent::__construct();
        $this->modelCreated = FALSE;
        $this->defaultClick = TRUE;
        $this->popover = FALSE;
        $this->groupColumn = NULL;
        $this->groupContent = NULL;
        $this->groupMask = NULL;
        $this->actions = array();
        $this->action_groups = array();
        $this->{'class'} = 'display responsive nowrap';
        $this->{'id'} = 'example';
        $this->cellspacing = '0';
        $this->width = '100%';
        $this->exportBtn = $exportBtn;
        $this->groupCount = 0;
        $this->actionWidth = '16px';
        $this->objects = array();
        $this->columnValues = array();
        // $this->{'id'}    = 'tdatagrid_' . mt_rand(1000000000, 1999999999);
    }

    public function enablePopover($title, $content)
    {
        $this->popover = TRUE;
        $this->poptitle = $title;
        $this->popcontent = $content;
    }

    public function makeScrollable()
    {
        $this->scrollable = TRUE;

        if (isset($this->thead)) {
            $this->thead->style = 'display: block';
        }
    }

    public function setActionWidth($width)
    {
        $this->actionWidth = $width;
    }

    public function disableDefaultClick()
    {
        $this->defaultClick = FALSE;
    }

    public function setHeight($height)
    {
        $this->height = $height;
    }

    public function addColumn(TDataGridColumn $object)
    {
        if ($this->modelCreated) {
            throw new Exception(AdiantiCoreTranslator::translate('You must call ^1 before ^2', __METHOD__, 'createModel'));
        } else {
            $this->columns[] = $object;
        }
    }

    public function getColumns()
    {
        return $this->columns;
    }

    public function addAction(TDataGridAction $object)
    {
        if (!$object->getField() && !$object->getFields()) {
            throw new Exception(AdiantiCoreTranslator::translate('You must define the field for the action (^1)', $object->toString()));
        }

        if ($this->modelCreated) {
            throw new Exception(AdiantiCoreTranslator::translate('You must call ^1 before ^2', __METHOD__, 'createModel'));
        } else {
            $this->actions[] = $object;
        }
    }

    public function addActionGroup(TDataGridActionGroup $object)
    {
        if ($this->modelCreated) {
            throw new Exception(AdiantiCoreTranslator::translate('You must call ^1 before ^2', __METHOD__, 'createModel'));
        } else {
            $this->action_groups[] = $object;
        }
    }

    public function getTotalColumns()
    {
        return count($this->columns) + count($this->actions) + count($this->action_groups);
    }

    public function setGroupColumn($column, $mask)
    {
        $this->groupColumn = $column;
        $this->groupMask = $mask;
    }

    function clear( $preserveHeader = TRUE )
    {
        if ($this->modelCreated) {

            if ($preserveHeader) {

                $copy = $this->children[0];
                $this->children = [];
                $this->children[] = $copy;

            } else {

                $this->children = [];

            }

            $this->tbody = new TElement('tbody');

            parent::add($this->tbody);

            $this->rowcount = 0;
            $this->objects = [];
            $this->columnValues = [];
            $this->groupContent = NULL;
        }
    }

    public function createModel( $create_header = TRUE )
    {
        if (!$this->columns) {
            return;
        }

        if ($create_header)
        {
            $thead = new TElement('thead');
            parent::add($thead);

            $row = new TElement('tr');
            if ($this->scrollable) {
                $this->thead->{'style'} = 'display:block';
            }
            $thead->add($row);

            $actions_count = count($this->actions) + count($this->action_groups);

            if ($actions_count > 0) {
                for ($n = 0; $n < $actions_count; $n++) {
                    $cell = new TElement('th');
                    $row->add($cell);
                    $cell->{'width'} = $this->actionWidth;
                }
            }

            if ($this->columns) {

                foreach ($this->columns as $column) {

                    $name = $column->getName();
                    $label = $column->getLabel();
                    $align = $column->getAlign();
                    $width = $column->getWidth();
                    $props = $column->getProperties();

                    $cell = new TElement('th');
                    $row->add($cell);
                    $cell->add($label);

                    if ($props) {
                        foreach ($props as $prop_name => $prop_value) {
                            $cell->$prop_name = $prop_value;
                        }
                    }

                    if ($width) {
                        $cell->{'width'} = (strpos($width, '%') !== false || strpos($width, 'px') !== false) ? $width : ($width + 8).'px';
                    }

                    if ($column->getAction()) {
                        $url = $column->getAction();
                        $cell->href = $url;
                        $cell->generator = 'adianti';
                    }

                }

                if ($this->scrollable) {
                    $cell = new TElement('td');
                    $row->add($cell);
                }

            }
        }

        $this->tbody = new TElement('tbody');

        parent::add($this->tbody);

        $this->modelCreated = TRUE;
    }

    public function getHead()
    {
        return $this->thead;
    }

    public function getBody()
    {
        return $this->tbody;
    }

    public function insert($position, $content)
    {
        $this->tbody->insert($position, $content);
    }

    public function addItems($objects)
    {
        if ($objects)
        {
            foreach ($objects as $object)
            {
                $this->addItem($object);
            }
        }
    }

    public function addItem($object)
    {
        if ($this->modelCreated) {

            if ($this->groupColumn AND ( is_null($this->groupContent) OR $this->groupContent !== $object->{$this->groupColumn} )) {

                $row = new TElement('tr');
                $row->{'class'} = 'tdatagrid_group';
                $row->{'level'} = ++ $this->groupCount;
                $this->groupRowCount = 0;

                $this->tbody->add($row);
                $cell = new TElement('td');
                $cell->add($this->replace($this->groupMask, $object));
                $cell->colspan = count($this->actions) + count($this->action_groups) + count($this->columns);
                $row->add($cell);
                $this->groupContent = $object->{$this->groupColumn};

            }

            $row = new TElement('tr');
            $this->tbody->add($row);

            if ($this->groupColumn) {
                $this->groupRowCount ++;
                $row->{'childof'} = $this->groupCount;
                $row->{'level'}   = $this->groupCount . '.'. $this->groupRowCount;
            }

            if ($this->actions) {

                foreach ($this->actions as $action_template) {

                    $action = clone $action_template;
                    $this->prepareAction($action, $object);

                    $label     = $action->getLabel();
                    $image     = $action->getImage();
                    $condition = $action->getDisplayCondition();

                    if (empty($condition) OR call_user_func($condition, $object)) {
                        $url = $action->serialize();
                        $first_url = isset($first_url) ? $first_url : $url;

                        $link = new TElement('a');
                        $link->{'href'}      = $url;
                        $link->{'generator'} = 'adianti';

                        if ($image) {

                            $image_tag = is_object($image) ? clone $image : new TImage($image);
                            $image_tag->{'title'} = $label;

                            if ($action->getUseButton()) {

                                $span = new TElement('span');
                                $span->{'class'} = $action->getButtonClass() ? $action->getButtonClass() : 'btn btn-default';
                                $span->add($image_tag);
                                $span->add($label);
                                $link->add($span);

                            } else {

                                $link->add( $image_tag );

                            }

                        } else {

                            $span = new TElement('span');
                            $span->{'class'} = $action->getButtonClass() ? $action->getButtonClass() : 'btn btn-default';
                            $span->add($label);
                            $link->add($span);

                        }

                    } else {

                        $link = '';

                    }

                    $cell = new TElement('td');
                    $row->add($cell);
                    $cell->add($link);
                    $cell->{'width'} = $this->actionWidth;

                }
            }

            if ($this->action_groups) {

                foreach ($this->action_groups as $action_group) {

                    $actions    = $action_group->getActions();
                    $headers    = $action_group->getHeaders();
                    $separators = $action_group->getSeparators();

                    if ($actions) {

                        $dropdown = new TDropDown($action_group->getLabel(), $action_group->getIcon());
                        $last_index = 0;

                        foreach ($actions as $index => $action_template) {

                            $action = clone $action_template;

                            for ($n = $last_index; $n < $index; $n++) {
                                if (isset($headers[$n])) {
                                    $dropdown->addHeader($headers[$n]);
                                }
                                if (isset($separators[$n])) {
                                    $dropdown->addSeparator();
                                }
                            }

                            $label = $action->getLabel();
                            $image = $action->getImage();
                            $condition = $action->getDisplayCondition();

                            if (empty($condition) OR call_user_func($condition, $object)) {

                                $this->prepareAction($action, $object);
                                $url = $action->serialize();
                                $first_url = isset($first_url) ? $first_url : $url;
                                $dropdown->addAction($label, $action, $image);

                            }

                            $last_index = $index;
                        }

                        $cell = new TElement('td');
                        $row->add($cell);
                        $cell->add($dropdown);

                    }
                }
            }

            if ($this->columns) {

                foreach ($this->columns as $column) {

                    $name     = $column->getName();
                    $align    = $column->getAlign();
                    $width    = $column->getWidth();
                    $function = $column->getTransformer();

                    if (substr($name,0,1) == '=') {

                        $content = $this->replace($name, $object, 'float');
                        $content = str_replace('+', ' + ', $content);
                        $content = str_replace('-', ' - ', $content);
                        $content = str_replace('*', ' * ', $content);
                        $content = str_replace('/', ' / ', $content);
                        $content = str_replace('(', ' ( ', $content);
                        $content = str_replace(')', ' ) ', $content);
                        $parser = new Parser;
                        $content = $parser->evaluate(substr($content,1));
                        $object->$name = $content;

                    } else {

                        try {

                            $content  = $object->$name;

                        } catch (Exception $e) {

                            $content = $this->replace($name, $object);

                            if (empty(trim($content)) OR $content === $name)
                            {
                                $content = $e->getMessage();
                            }

                        }

                    }

                    if (isset($this->columnValues[$name])) {
                        $this->columnValues[$name][] = $content;
                    } else {
                        $this->columnValues[$name] = [$content];
                    }

                    $data = is_null($content) ? '' : $content;

                    if ($function) {
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
                        $div->{'pkey'}   = $editaction_field;
                        $div->add($data);
                        $cell = new TElement('td');
                        $row->add($cell);
                        $cell->add($div);

                    } else {

                        $cell = new TElement('td');
                        $row->add($cell);
                        $cell->add($data);
                        $cell->{'align'} = $align;

                        if (isset($first_url) AND $this->defaultClick) {
                            $cell->{'href'}      = $first_url;
                            $cell->{'generator'} = 'adianti';
                        }
                    }

                    if ($width) {
                        $cell->{'width'} = (strpos($width, '%') !== false || strpos($width, 'px') !== false) ? $width : ($width + 8).'px';
                    }
                }
            }

            if ($this->popover) {

                $poptitle   = $this->poptitle;
                $popcontent = $this->popcontent;
                $poptitle   = $this->replace($poptitle, $object);
                $popcontent = $this->replace($popcontent, $object);

                $methods = get_class_methods($object);

                if($methods){
                    foreach ($methods as $method){
                        if (stristr($popcontent, "{$method}()") !== FALSE){
                            $popcontent = str_replace('{'.$method.'()}', $object->$method(), $popcontent);
                        }
                    }
                }

                $row->{'popover'} = 'true';
                $row->{'poptitle'} = $poptitle;
                $row->{'popcontent'} = htmlspecialchars(str_replace("\n", '', nl2br($popcontent)));
            }

            $this->objects[$this->rowcount] = $object;

            $this->rowcount ++;

            return $row;

        } else {

            throw new Exception(AdiantiCoreTranslator::translate('You must call ^1 before ^2', 'createModel', __METHOD__));

        }
    }

    public function getItems()
    {
        return $this->objects;
    }

    private function processTotals()
    {
        if (count($this->objects) == 0) {
            return;
        }

        $has_total = false;

        $tfoot = new TElement('tfoot');
        $tfoot->{'class'} = 'tdatagrid_footer';

        $row = new TElement('tr');
        $tfoot->add($row);

        if ($this->actions) {
            foreach ($this->actions as $action) {
                $cell = new TElement('td');
                $row->add($cell);
            }
        }

        if ($this->action_groups)
        {
            foreach ($this->action_groups as $action_group)
            {
                $cell = new TElement('td');
                $row->add($cell);
            }
        }

        if ($this->columns)
        {
            // iterate the DataGrid columns
            foreach ($this->columns as $column)
            {
                $cell = new TElement('td');
                $row->add($cell);

                // get the column total function
                $totalFunction = $column->getTotalFunction();
                $transformer   = $column->getTransformer();
                $name          = $column->getName();
                $align         = $column->getAlign();
                $cell->{'style'} = "text-align:$align";
                if ($totalFunction)
                {
                    $has_total = true;
                    $content   = $totalFunction($this->columnValues[$name]);

                    if ($transformer)
                    {
                        // apply the transformer functions over the data
                        $content = call_user_func($transformer, $content, null, null);
                    }
                    $cell->add($content);
                }
                else
                {
                    $cell->add('&nbsp;');
                }
                //$cell->{'class'} = 'tdatagrid_action';
            }
        }

        if ($has_total)
        {
            parent::add($tfoot);
        }
    }

    private function replace($content, $object, $cast = NULL)
    {
        if (preg_match_all('/\{(.*?)\}/', $content, $matches)) {

            foreach ($matches[0] as $match) {

                $property = substr($match, 1, -1);
                $value    = $object->$property;

                if ($cast) {
                    settype($value, $cast);
                }

                $content  = str_replace($match, $value, $content);

            }

        }

        return $content;
    }

    public function getRowIndex($attribute, $value)
    {
        foreach ($this->objects as $pos => $object) {
            if ($object->$attribute == $value) {
                return $pos;
            }
        }

        return NULL;
    }

    public function getRow($position)
    {
        return $this->tbody->get($position);
    }

    protected function prepareAction(TAction $action, $object)
    {
        $field  = $action->getField();

        if ( is_null( $field ) ) {
            throw new Exception(AdiantiCoreTranslator::translate('Field for action ^1 not defined', $label) . '.<br>' .
                                AdiantiCoreTranslator::translate('Use the ^1 method', 'setField'.'()').'.');
        }

        if ( !isset( $object->$field ) ) {
            throw new Exception(AdiantiCoreTranslator::translate('Field ^1 not exists or contains NULL value', $field));
        }

        $action->setParameter('key', isset($object->$field) ? $object->$field : NULL);

        if ( isset( $object->$field ) ) {
            $action->setParameter($field, $object->$field);
        }

        $fieldfk = $action->getFk();

        if ( isset( $fieldfk ) ) {

            if ( !isset( $object->$fieldfk ) ) {
                throw new Exception( AdiantiCoreTranslator::translate( 'FK ^1 not exists', $field ) );
            }

            $action->setParameter( 'fk', isset( $object->$fieldfk ) ? $object->$fieldfk : NULL );
        }

        $fielddid = $action->getDid();

        if ( isset( $fielddid ) ) {

            if ( !isset( $object->$fielddid ) ) {
                throw new Exception( AdiantiCoreTranslator::translate( 'DID ^1 not exists', $fielddid ) );
            }

            $action->setParameter( 'did', isset( $object->$fielddid ) ? $object->$fielddid : NULL );
        }
    }

    public function getWidth()
    {
        $width = 0;
        if ($this->actions) {
            foreach ($this->actions as $action) {
                $width += 22;
            }
        }

        if ($this->columns) {
            foreach ($this->columns as $column) {
                if (is_numeric($column->getWidth())) {
                    $width += $column->getWidth();
                }
            }
        }

        return $width;
    }

    function show()
    {
        $this->processTotals();

        parent::show();

        $params = $_REQUEST;
        unset($params['class']);
        unset($params['method']);

        $urlparams = '&' . http_build_query($params);

        TScript::create('
            $(function() {
        	  $(".inlineediting").editInPlace({
        		callback: function(unused, enteredText) {
        		    __adianti_load_page($(this).attr("action")+"'.$urlparams.'&key="+$(this).attr("key")+"&field="+$(this).attr("field")+"&value="+encodeURIComponent(enteredText));
        		    return enteredText;
        		},
        		show_buttons: false,
        		text_size:20,
        	    params:column=name
    	      });
            });
        ');

        if ($this->exportBtn == FALSE) {
            TPage::include_js('app/lib/include/initDataTable.js');
        } else {
            TScript::create('
                $(document).ready(function() {
                  $("#example").dataTable();
                } );
            ');
        }
    }

    public function setPageNavigation($pageNavigation)
    {
        $this->pageNavigation = $pageNavigation;
    }

    public function getPageNavigation()
    {
        return $this->pageNavigation;
    }

    public function addQuickColumn($label, $name, $align = 'left', $size = 200, TAction $action = NULL, $param = NULL)
    {
        $object = new TDataGridColumn($name, $label, $align, $size);

        if ($action instanceof TAction) {
            $action->setParameter($param[0], $param[1]);
            $object->setAction($action);
        }

        $this->addColumn($object);

        return $object;
    }

    public function addQuickAction($label, TDataGridAction $action, $field, $icon = NULL)
    {
        $action->setLabel($label);

        if ($icon) {
            $action->setImage($icon);
        }

        if (is_array($field)) {
            $action->setFields($field);
        } else {
            $action->setField($field);
        }

        $this->addAction($action);

        return $action;
    }
}
