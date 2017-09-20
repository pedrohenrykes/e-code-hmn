<?php

class SystemUser extends TRecord
{
    const TABLENAME = 'usuarios';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'serial';

    private $frontpage;
    private $unit;
    private $system_user_groups = array();
    private $system_user_programs = array();
    private $profissional;

    public function __construct($id = NULL)
    {
        parent::__construct($id);
        parent::addAttribute('name');
        parent::addAttribute('login');
        parent::addAttribute('password');
        parent::addAttribute('email');
        parent::addAttribute('frontpage_id');
        parent::addAttribute('system_unit_id');
        parent::addAttribute('active');
        parent::addAttribute('profissional_id');
    }

    public function get_nomeprofissional()
    {
        if( empty( $this->profissional ) ) {
            $this->profissional = new ProfissionalRecord( $this->profissional_id );
        }

        return $this->profissional->nomeprofissional;
    }

    public function get_tipoprofissional_id()
    {
        if( empty( $this->profissional ) ) {
            $this->profissional = new ProfissionalRecord( $this->profissional_id );
        }

        return $this->profissional->tipoprofissional_id;
    }

    public function get_frontpage_name()
    {
        if ( empty( $this->frontpage ) ) {
            $this->frontpage = new SystemProgram( $this->frontpage_id );
        }

        return $this->frontpage->name;
    }

    public function get_frontpage()
    {
        if ( empty( $this->frontpage ) ) {
            $this->frontpage = new SystemProgram( $this->frontpage_id );
        }

        return $this->frontpage;
    }

    public function get_unit()
    {
        if ( empty( $this->unit ) ) {
            $this->unit = new SystemUnit( $this->system_unit_id );
        }

        return $this->unit;
    }

    public function addSystemUserGroup( SystemGroup $systemusergroup )
    {
        $object = new SystemUserGroup;
        $object->system_group_id = $systemusergroup->id;
        $object->system_user_id = $this->id;
        $object->store();
    }

    public function getSystemUserGroups()
    {
        $groups = [];

        $repository = new TRepository( 'SystemUserGroup' );
        $criteria = new TCriteria;
        $criteria->add( new TFilter( 'system_user_id', '=', $this->id ) );
        $objects = $repository->load( $criteria );

        if ( $objects ) {
            foreach ( $objects as $object ) {
                $groups[] = new SystemGroup( $object->system_group_id );
            }
        }

        return $groups;
    }

    public function addSystemUserProgram( SystemProgram $systemprogram )
    {
        $object = new SystemUserProgram;
        $object->system_program_id = $systemprogram->id;
        $object->system_user_id = $this->id;
        $object->store();
    }

    public function getSystemUserPrograms()
    {
        $programs = [];

        $repository = new TRepository( 'SystemUserProgram' );
        $criteria = new TCriteria;
        $criteria->add( new TFilter( 'system_user_id', '=', $this->id ) );
        $objects = $repository->load( $criteria );

        if ( $objects ) {
            foreach ( $objects as $object ) {
                $programs[] = new SystemProgram( $object->system_program_id );
            }
        }

        return $programs;
    }

    public function getSystemUserGroupIds()
    {
        $groupids = [];

        $groups = $this->getSystemUserGroups();

        if ( $groups ) {
            foreach ( $groups as $group ) {
                $groupids[] = $group->id;
            }
        }

        return implode( ',', $groupids );
    }

    public function getSystemUserGroupNames()
    {
        $groupnames = [];

        $groups = $this->getSystemUserGroups();

        if ( $groups ) {
            foreach ( $groups as $group ) {
                $groupnames[] = $group->name;
            }
        }

        return implode( ',', $groupnames );
    }

    public function clearParts()
    {
        $criteria = new TCriteria;
        $criteria->add( new TFilter( 'system_user_id', '=', $this->id ) );

        $repository = new TRepository( 'SystemUserGroup' );
        $repository->delete( $criteria );

        $repository = new TRepository( 'SystemUserProgram' );
        $repository->delete( $criteria );
    }

    public function delete( $id = NULL )
    {
        $id = isset( $id ) ? $id : $this->id;
        $repository = new TRepository( 'SystemUserGroup' );
        $criteria = new TCriteria;
        $criteria->add( new TFilter( 'system_user_id', '=', $id ) );
        $repository->delete( $criteria );

        $id = isset( $id ) ? $id : $this->id;
        $repository = new TRepository( 'SystemUserProgram' );
        $criteria = new TCriteria;
        $criteria->add( new TFilter( 'system_user_id', '=', $id ) );
        $repository->delete( $criteria );

        parent::delete( $id );
    }

    public static function authenticate( $login, $password )
    {
        $user = self::newFromLogin( $login );

        if ( $user instanceof SystemUser ) {

            if ( $user->active == 'N' ) {
                throw new Exception( _t( 'Inactive user' ) );
            } else if ( isset( $user->password ) AND ( $user->password == md5( $password ) ) ) {
                return $user;
            } else {
                throw new Exception( _t( 'Wrong password' ) );
            }

        } else {

            throw new Exception( _t( 'User not found' ) );

        }
    }

    static public function newFromLogin( $login )
    {
        $repos = new TRepository( 'SystemUser' );
        $criteria = new TCriteria;
        $criteria->add(new TFilter( 'login', '=', $login ) );
        $objects = $repos->load( $criteria );

        if ( isset( $objects[0] ) ) {
            return $objects[0];
        }
    }

    public function getPrograms()
    {
        $programs = [];

        foreach( $this->getSystemUserGroups() as $group ) {
            foreach( $group->getSystemPrograms() as $prog ) {
                $programs[ $prog->controller ] = true;
            }
        }

        foreach( $this->getSystemUserPrograms() as $prog ) {
            $programs[ $prog->controller ] = true;
        }

        return $programs;
    }

    public function getProgramsList()
    {
        $programs = [];

        foreach( $this->getSystemUserGroups() as $group ) {
            foreach( $group->getSystemPrograms() as $prog ) {
                $programs[ $prog->controller ] = $prog->name;
            }
        }

        foreach( $this->getSystemUserPrograms() as $prog ) {
            $programs[ $prog->controller ] = $prog->name;
        }

        asort( $programs );

        return $programs;
    }

    public function checkInGroup( SystemGroup $group )
    {
        $groups = [];

        foreach( $this->getSystemUserGroups() as $user_group ) {
            $groups[] = $user_group->id;
        }

        return in_array( $group->id, $groups );
    }

    public static function getInGroups( $groups )
    {
        $collection = [];

        $users = self::all();

        if ( $users ) {
            foreach ( $users as $user ) {
                foreach ( $groups as $group ) {
                    if ( $user->checkInGroup( $group ) ) {
                        $collection[] = $user;
                    }
                }
            }
        }

        return $collection;
    }
}
