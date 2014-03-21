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
        public function arrayToEntities( array $rows )
        {
            if( is_array( $rows ) )
            {
                foreach( $rows as $key=>$row )
                {
                    $rows[ $key ] = $this->toEntity( $row );
                }
            }

            return $rows;
        }

//--------------------------------------------------------------------------------------------------------------------//
        public function get( $id )
        {
            return $this->getEntity( $id );
        }

//--------------------------------------------------------------------------------------------------------------------//
        public function getAll( $count = null, $offset = null )
        {
            return $this->arrayToEntities( parent::getAll( $count, $offset ) );
        }

//--------------------------------------------------------------------------------------------------------------------//
        public function getAllBy( $field, $value )
        {
            return $this->arrayToEntities( parent::getAllBy( $field, $value ) );
        }

//--------------------------------------------------------------------------------------------------------------------//
        /**
         *  Get empty entity/ Can be handy in row creation
         *
         * @return \Kalibri\Model\Entity
         */
        public function getEmpty()
        {
            if( !$this->_entityClass )
            {
                $class = get_class( $this );
                $pos = strrpos( $class, '\\' );

                $this->_entityClass = substr( $class, 0, $pos ).'\\Entity'.substr( $class, $pos );
            }

            return new $this->_entityClass();
        }

//------------------------------------------------------------------------------------------------//
        public function getEntity( $id, $field = null )
        {
            if( is_object( $id ) )
            {
                return $id;
            }

            if( ( $data = parent::get( (int)$id, $field ) ) && is_array( $data ) )
            {
                $data = $this->toEntity( $data );
            }

            return $data;
        }
    }
}