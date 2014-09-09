<?php

namespace Kalibri {

    /**
     *  @package Kalibri
     *
     *  @author <a href="mailto:kostinenko@gmail.com">Alexander Kostynenko</a>
     */
	class Benchmark
	{
		const RESULTS_MARK = '-RESULTS-';

		/**
		 * @var array
		 */
		private $_marks = array();

//------------------------------------------------------------------------------------------------//
        /**
         * Start benchmark point
         *
         * @param $name
         * @param string $comment
         * @internal param \Kalibri\name $string
         */
		public function start( $name, $comment = null )
		{
			if( !isset( $this->_marks[ $name ] ) )
			{
				$firstMark = current( $this->_marks );

				$this->_marks[ $name ] = array(
					'start'        => microtime(true),
					'stop'         => false,
					'memory_start' => $this->memoryUsage(),
					'memory_stop'  => false,
					'execution'    => 0,
					'memory_used'  => null,
					'comment'      => $comment,
					'start_offset' => is_array( $firstMark )? microtime(true) - $firstMark['start']: 0,
					'starts'       => 1
				);
			}
			else
			{
				if( !$this->_marks[ $name ]['stop'] )
				{
					$this->stop( $name );
				}

				$this->_marks[ $name ]['start'] = microtime(true);
				$this->_marks[ $name ]['starts']++;
			}
		}

//------------------------------------------------------------------------------------------------//
		/**
		 * Stop benchmark point
		 *
		 * @param string $name
		 */
		public function stop( $name )
		{
			if( isset( $this->_marks[$name]) && $this->_marks[$name]['stop'] === false)
			{
				$this->_marks[$name]['stop'] = microtime(true);
				$this->_marks[$name]['memory_stop'] = $this->memoryUsage();
				$this->_marks[$name]['execution'] += $this->_marks[$name]['stop'] - $this->_marks[$name]['start'];
				$this->_marks[$name]['memory_used'] = $this->_marks[$name]['memory_stop'] - $this->_marks[$name]['memory_start'];

				return $this->_marks[ $name ];
			}
			//\Kalibri::logger()->add(\Kalibri\Logger::WARNING, "Benchmark mark not found '$name'");
			//throw new Exception("Benchmark mark not found '$name'");
		}

//------------------------------------------------------------------------------------------------//
		/**
		 * Get single benchmark point or full points list
		 *
		 * @param string $name
		 *
		 * @return array
		 */
		public function get( $name )
		{
			return isset( $this->_marks[ $name ] )? $this->_marks[ $name ]: null;
		}

//------------------------------------------------------------------------------------------------//
		/**
		 * Get memory usage
		 *
		 * @return float
		 */
		public function memoryUsage()
		{
			static $func;

			if( $func === null )
			{
				// Test if memory usage can be seen
				$func = function_exists('memory_get_usage');
			}

			return $func ? memory_get_usage() : 0;
		}

//------------------------------------------------------------------------------------------------//
		/**
		 * Get all marks. Allmost the same as Kalibri\Benchmark::get(TRUE)
		 * but point will not autocomplete
		 *
         * @param bool @stopAll
         *
		 * @return array
		 */
		public function getMarks( $stopAll = false )
		{
			if( $stopAll )
			{
				$total = array(
					'time'=>0,
					'memory'=>0,
					'marks'=>0,
					'peak_memory'=>0,
					'includes'=>array()
				);

				if( function_exists('memory_get_peak_usage') )
				{
					$total['peak_memory'] = memory_get_peak_usage(false);
				}

				if( function_exists('get_included_files') )
				{	
					$total['includes'] = get_included_files();
					$basePath = substr( \Kalibri::app()->getLocation(), 0, -( strlen( \Kalibri::app()->getNamespace() ) )-2 );
					$includesCount = count( $total['includes'] );

					for( $i=0; $i < $includesCount; $i++ )
					{
						$total['includes'][ $i ] = str_replace( $basePath, '', $total['includes'][ $i ] );
					}
				}

				foreach( $this->_marks as $name=>$mark )
				{
					if( !$mark['stop'] )
					{
						$mark = $this->stop( $name );
					}

					$total['time'] += $mark['execution'];
					$total['memory'] += $mark['memory_used'];
				}

				$total['marks'] = count( $this->_marks );
				$this->_marks[ self::RESULTS_MARK ] = $total;
			}

			return $this->_marks;
		}
	}
}