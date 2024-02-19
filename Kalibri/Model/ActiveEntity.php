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
        #[\Override]
        public function get( $id )
        {
            return $this->getEntity( $id );
        }

//--------------------------------------------------------------------------------------------------------------------//
        #[\Override]
        public function getSingleBy( $field, $id )
        {
            $result = parent::getSingleBy($field, $id);
            return $result? $this->toEntity($result): null;
        }

//--------------------------------------------------------------------------------------------------------------------//
        #[\Override]
        public function getAll( $count = null, $offset = null )
        {
            return $this->arrayToEntities( parent::getAll( $count, $offset ) );
        }

//--------------------------------------------------------------------------------------------------------------------//
        #[\Override]
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
                $class = static::class;
                $pos = strrpos( $class, '\\' );

                $this->entityClass = substr( $class, 0, $pos ).'\\Entity'.substr( $class, $pos );
            }

            return new $this->entityClass();
        }

//--------------------------------------------------------------------------------------------------------------------//
        #[\Override]
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


//--------------------------------------------------------------------------------------------------------------------//
        #[\Override]
        public function getBy( $id, $field = null )
        {
            $result = parent::getBy($id, $field);

            if( $result )
            {
                return $this->toEntity($result);
            }

            return false;
        }

//--------------------------------------------------------------------------------------------------------------------//
        #[\Override]
        function getAllWithPrimaryAsKey()
        {
            $result = [];
            $dbResult = $this->getAll();

            foreach($dbResult as $row)
            {
                $result[ $row->getPrimaryValue() ] = $row;
            }

            return $result;
        }
    }
}