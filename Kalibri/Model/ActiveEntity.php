<?php

namespace Kalibri\Model {

	class ActiveEntity extends Active
    {
//--------------------------------------------------------------------------------------------------------------------//
        /**
         *  Transform list of rows to list of entities
         *
         * @param $rows array
         *
         * @return \Kalibri\Model\Entity[]
         */
        public function arrayToEntities( $rows )
        {
            $result = [];

            if( is_array( $rows ) )
            {
                foreach( $rows as $key=>$row )
                {
                    $result[ $key ] = $this->toEntity( $row );
                }
            }

            return $result;
        }

//--------------------------------------------------------------------------------------------------------------------//
        public function get( $id )
        {
            return $this->getEntity( $id );
        }

//--------------------------------------------------------------------------------------------------------------------//
        public function getSingleBy( $field, $id )
        {
            $result = parent::getSingleBy($field, $id);
            return $result? $this->toEntity($result): null;
        }

//--------------------------------------------------------------------------------------------------------------------//
        public function getAll( $count = null, $offset = null )
        {
            return $this->arrayToEntities( parent::getAll( $count, $offset ) );
        }

//--------------------------------------------------------------------------------------------------------------------//
        public function getAllBy( $field, $value )
        {
            $result = parent::getAllBy( $field, $value );
            return $result? $this->arrayToEntities( $result ): null;
        }

//--------------------------------------------------------------------------------------------------------------------//
        /**
         *  Get empty entity. Can be handy in row creation
         *
         * @return \Kalibri\Model\Entity
         */
        public function getEmpty()
        {
            if( !$this->entityClass )
            {
                $class = get_class( $this );
                $pos = strrpos( $class, '\\' );

                $this->entityClass = substr( $class, 0, $pos ).'\\Entity'.substr( $class, $pos );
            }

            return new $this->entityClass();
        }

//------------------------------------------------------------------------------------------------//
        public function getEntity( $id, $field = null )
        {
            if( is_object( $id ) )
            {
                return $id;
            }

            if( ( $data = parent::getBy( $id, $field ) ) && is_array( $data ) )
            {
                $data = $this->toEntity( $data );
            }

            return $data;
        }

//------------------------------------------------------------------------------------------------//
        public function getBy( $id, $field = null )
        {
            $result = parent::getBy($id, $field);

            if( $result )
            {
                return $this->toEntity($result);
            }

            return false;
        }
    }
}