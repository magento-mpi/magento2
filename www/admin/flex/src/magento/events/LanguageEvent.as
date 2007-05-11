package magento.events
{
	import flash.events.Event;

	public class LanguageEvent extends Event
	{
		/**
		 * Event types
		 */
		public static const LANGUAGE_LOADED:String = "languageLoaded"; 		// Loaded selected language file
		
		public function LanguageEvent(type:String, bubbles:Boolean=false, cancelable:Boolean=false)
		{
			super(type, bubbles, cancelable);
		}
		
	}
}