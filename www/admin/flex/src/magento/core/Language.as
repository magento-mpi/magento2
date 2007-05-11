package magento.core
{
	import flash.events.EventDispatcher;
	import flash.display.DisplayObject;
	import mx.rpc.events.ResultEvent;
	import mx.utils.StringUtil;
	import mx.collections.ArrayCollection;
	import magento.events.LanguageEvent;
	import magento.rpc.Request;
	import magento.core.StringFormater;
	import magento.core.Config;
	
	/**
	 * Component for multilangual interface
	 * 
	 * Rewrited for magenta
	 * 1. Loading multiple files;
	 * 2. Updated for magenta engine;
	 * 3. Language list future removed.
	 */
	public class Language extends EventDispatcher
	{
		/**
		 * Languege's phrases list
		 */
		private var phrases:Object = new Object();
		
		/**
		 * Loaded language's files
		 */
		private var loadedFiles:Object = new Object();
		/**
		 * RPC object
		 */
		private var requestObject:Request;
		/**
		 * Constructor
		 * @param parent parent object for position of status window (default Application)
		 */
		public function Language(parent:DisplayObject)
		{
			super();
			requestObject = new Request(parent);
		}
		
		/**
		 * Returns phrase of current language with name "name"
		 * @param name phrase name
		 */
		[Bindable(event='languageLoaded')]
		public function getPhrase(name:String):String
		{
			if(phrases[name] == null)
				return "!!!("+name+")"; 
			
			return String(phrases[name]);
		}
		
		/**
		 * Returns phrase of current language with name "name" and replaces in 
		 * constructions like {property.property} with same named properties of object "data"
		 * @param name phrase name
		 * @param data replacement object
		 */
		[Bindable(event='languageLoaded')]
		public function getPhraseFormat(name:String, data:Object):String
		{
			var lang:String = getPhrase(name);
			
			lang = StringFormater.format(lang, data);
			
			return lang;
		}
		
		
		/**
		 * If selected language's file not loaded, load it. 
		 * @param filePath language's file
		 */
		public function load( filePath:String ):void
		{
			if(!loadedFiles[filePath])
			{
				requestObject.url = Config.getVar('basePath') + Config.getVar('languagePath') + filePath ;
				requestObject.method = "get";
				requestObject.showStatusWindow = true;
				
				// Inform user about language loading
				requestObject.statusText = 	"Language loading...";
				
				requestObject.addEventListener(ResultEvent.RESULT, languageLoaded);
				requestObject.send();
			
				loadedFiles[filePath] = true;
			}
			else
			{
				// Inform listeners that language loaded
				dispatchEvent(new LanguageEvent(LanguageEvent.LANGUAGE_LOADED));
			}
		}
		
		/**
		 * Receives loaded language phrases
		 * @param event response event
		 */
		private function languageLoaded(event:ResultEvent):void
		{
			if( event.result.language.phrase is ArrayCollection )
			{
				for each(var phrase:Object in event.result.language.phrase)
				{
					phrases[phrase.name] = StringUtil.trim(phrase.value);
				}
			}
			else
			{
				phrases[event.result.language.phrase.name] = StringUtil.trim(event.result.language.phrase.value);
			}				
			requestObject.removeEventListener(ResultEvent.RESULT, languageLoaded);
			
			// Inform listeners that language loaded
			dispatchEvent(new LanguageEvent(LanguageEvent.LANGUAGE_LOADED));
			
		}
		
	}
}