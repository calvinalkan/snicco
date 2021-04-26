<?php


	namespace WPEmerge\ViewComposers;

	use WPEmerge\Contracts\Handler;
	use WPEmerge\Handlers\AbstractFactory;

	class ViewComposerFactory extends AbstractFactory {


		public function createUsing( $raw_handler ) : Handler {


			$handler = $this->normalizeInput( $raw_handler );

			$handler = [$handler[0], $handler[1] ?? 'compose'];

			if ( $handler[0] instanceof \Closure ) {

				return new ViewComposer( $this->wrapClosure( $handler[0] ) );

			}

			if ( $namespaced_handler = $this->checkIsCallable( $handler ) ) {

				return new ViewComposer($this->wrapClass($namespaced_handler));

			}

			$this->fail( $handler[0], $handler[1] );

		}

	}