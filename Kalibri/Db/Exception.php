<?php

namespace Kalibri\Db {

	class Exception extends \Exception {
		protected $query;
		protected $queryParams;

		public function setQueryInfo( $query, $params = null )
		{
			$this->query = $query;
			$this->queryParams = $params;
			return $this;
		}

		public function getQuery()
		{
			return $this->query;
		}

		public function getQueryParams()
		{
			return $this->queryParams;
		}
	}
}