package magento.core
{
	 /**
	  * Component for using singletons
	  */
	public final class Singleton
	{
	    private static var objectMap:Object = {};

	   /**
		* Add instance of "clazz" in singleton list with alias "name"
		* @param name alias for instance of "clazz"
		* @param clazz class 
		* @param parameter first argument for "clazz" constructor
		*/
	    
	    public static function registerClass(name:String, clazz:Class, parameter:* = null):void
	    {
	        if ( !isRegistered(name) )
	        {
	        	if(parameter != null)
		            objectMap[name] = new clazz(parameter);
		        else
		        	objectMap[name] = new clazz;
	        }
	    }

		/**
		 * Returns true if alias "name" already registered.
		 * @param name alias for singleton
		 */
		
		public static function isRegistered(name:String):Boolean
		{
			if( !objectMap[name] ) 
				return false;
			
			return true;
		}
		
		
	   /**
		* Returns singleton with alias "name"
		* @param name registered alias 
		*/
	    
	    public static function getInstance(name:String):*
		{
			if( !isRegistered(name) )
				return null;
			
		    return objectMap[name];
		}
		
		/**
		 * Destroy singleton with alias "name"
		 * @param name registered alias 
		 */
		 
		 public static function unregisterClass( name:String ):void
		 {
		 	if( !isRegistered(name) ) 
		 		return;
		 	
		 	delete(objectMap[name]);
		 }

	}
}