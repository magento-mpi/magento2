package magento.rpc
{
	import mx.rpc.http.mxml.HTTPService;
	import mx.rpc.events.ResultEvent;
	import flash.display.DisplayObject;
	import mx.rpc.AsyncToken;

	/**
	 * Customized request component
	 */
	public class Request extends HTTPService
	{
		/**
		 * Display state for status window 
		 */
		private var _showStatusWindow:Boolean = false;
		
		/**
		 * Satus window object
		 */
		private var statusWindow:RequestInfoWindow;
		
		
		/**
		 * Cunstructor
		 * @param parent parent object for position of status window (default Application)
		 */
		public function Request(parent:DisplayObject):void
		{
			super();
			showBusyCursor = true; // Show busy cursor by default
			statusWindow = new RequestInfoWindow(parent); // Craete object for status window
			
			addEventListener(ResultEvent.RESULT, close, false, 2.0);
		}
		
		/**
		 * Set the display state of status window
		 * @param value display state
		 */
		public function set showStatusWindow(value:Boolean):void
		{
			_showStatusWindow = value;
		}
		/**
		 * Returns display state of status vindow
		 */
		public function get showStatusWindow():Boolean
		{
			return _showStatusWindow;
		}
		
		/**
		 * Set the status window text 
		 * @param value текст
		 */
		public function set statusText(value:String):void
		{
			statusWindow.statusLabel = value;
		}
		/**
		 * Return the status window text
		 */
		public function get statusText():String
		{
			return statusWindow.statusLabel;
		}
		
		/**
		 * Send request to some server backend
		 * @param parameters Url parameters
		 */ 
		 
		override public function send(parameters:Object=null):AsyncToken
		{
			if(parameters != null)
				prepareParametersForPHP(parameters);
			
			var token:AsyncToken = super.send(parameters);
			open();
			return token;
		}
		
		/**
		 * Converts Flex objects to PHP valid parameters
		 * 
		 * @param parameters url parameters
		 */
		private function prepareParametersForPHP(parameters:Object):Object
		{
			var k:Number = 0;
			var j:String = '';
			var toDelete:Array = [];		  // Converted parameters those must be deleted
			var needContinue:Boolean = false; // Call function again indetifier
			for(var i:String in parameters)
			{
				if( 
					(parameters[i] is Object || parameters[i] is Array) 
					&& 
					!(parameters[i] is String) 
					&& 
					!(parameters[i] is Number) 
					&& 
					!(parameters[i] is Boolean))
				{
					k = 0;
					for(j in parameters[i])
					{
						if(!(parameters[i] is Array))
						{
							parameters[i+'['+j+']'] = parameters[i][j];	
						}
						else
						{
							parameters[i+'['+k+']'] = parameters[i][j];	
							k++;
						}
					}
					
					needContinue = true;
					toDelete.push(i);
				}
				else if(parameters[i] is Boolean)
				{
					parameters[i] = ( parameters[i] ? 'true' : 'false' );
				}
				
			}
			
			for(j in toDelete)
			{
				delete(parameters[toDelete[j]]); // Delete parameters
			}
			
			if(needContinue)
				// If we have not converted parameters call function one more time
				return prepareParametersForPHP(parameters); 
			else
				return parameters;
		}
		
		/**
		 * Called when rpc connection opened and (if needed) display status window
		 */
		private function open( ):void
		{
			if(_showStatusWindow)
			{
				statusWindow.show();
			}
		}
		
		/**
		 * Called when server result geted, used for hiding of status window
		 * @param event resonse event
		 */
		private function close(event:ResultEvent):void
		{
			if(_showStatusWindow)
			{
				statusWindow.hide();
			}
		}
		
	}
}