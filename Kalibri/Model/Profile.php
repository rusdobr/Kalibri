<?php

namespace Kalibri\Model;

use \Kalibri\Model\Entity\Profile as ProfileEntity;

class Profile extends ActiveEntity
{

//------------------------------------------------------------------------------------------------//
    /**
     * @param string $login
     *
     * @return \Kalibri\Model\Entity\Profile
     */
    public function getByLogin( $login )
    {
        $result = $this->getBy($login, 'login');
        return is_array($result)? $this->toEntity( $result ): null;
    }

//------------------------------------------------------------------------------------------------//
    public function register( ProfileEntity $data )
    {

        if( $data->getLogin() && $data->getPassword() )
        {
            $this->insert( $data );
            return true;
        }

        return false;
    }
}