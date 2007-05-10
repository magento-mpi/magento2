package magento.events
{
	import flash.events.Event;

	public class LanguageEvent extends Event
	{
		/**
		 * Event types
		 */
		public static const LANGUAGE_LOADED:String = "languageLoaded"; 		// Loaded selected language
		public static const LANGUAGES_LOADED:String = "languagesLoaded"; 	// Loaded languages' list
		public static const LANGUAGE_UPDATED:String = "languageUpdated";	// Current language changed
		public static const LANGUAGES_LIST:String = "languagesList";		// Languages' ArrayCollection updated
		
		public var currentLanguage:String = null; // Current language
		public function LanguageEvent(type:String, currentLanguage:String = null, bubbles:Boolean=false, cancelable:Boolean=false)
		{
			super(type, bubbles, cancelable);
			this.currentLanguage = currentLanguage;
		}
		
	}
}