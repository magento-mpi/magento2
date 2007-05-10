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
	
	/**
	 * Component for multilangual interface
	 * 
	 * TODO: Udaptate to magenta engine
	 */
	public class Language extends EventDispatcher
	{
		/**
		 * Lagueges list with phrases list
		 */
		private var languages:Object = new Object();
		/**
		 * Current language
		 */
		private var currentLanguage:String = null;
		/**
		 * RPC object
		 */
		private var requestObject:Request;
		/**
		 * Directory on server where languages files placed
		 */
		private var languagesDirectory:String = "";
		/**
		 * Languages list for ComboBox (or another component)
		 */
		private var _languageList:ArrayCollection = null;
		
		/**
		 * Constructor
		 * @param parent parent object for position of status window (default Application)
		 */
		public function Language(parent:DisplayObject)
		{
			super();
			requestObject = new Request(parent);
			loadLanguage(); 
			addEventListener(LanguageEvent.LANGUAGE_UPDATED,  loadLanguage);
		}
		
		/**
		 * Returns phrase of current language with name "name"
		 * @param name phrase name
		 */
		[Bindable(event='languageLoaded')]
		public function getPhrase(name:String):String
		{
			if(languages[currentLanguage].phrases[name] == null)
				return "!!!("+name+")"; 
			
			return String(languages[currentLanguage].phrases[name]);
		}
		
		/**
		 * Returns phrase of current language with name "name" and replaces in 
		 * t constructions like {property} with same named properties of object "data"
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
		 * Send request for language list
		 */
		private function loadLanguages():void
		{
			requestObject.url = "/admin/mage_report/lang/langListXml";
			requestObject.method = "get";
			requestObject.showStatusWindow = true;
			requestObject.statusText = "Language loading...";
			requestObject.addEventListener(ResultEvent.RESULT, languagesLoaded);
			requestObject.send();
			
		}
		/**
		 * Receives languages list
		 * @param event response event
		 */	
		private function languagesLoaded(event:ResultEvent):void
		{
			languagesDirectory = event.result.languages.directory; 
			var setLanguageName:String = null; 
			for each(var langnode:Object in event.result.languages.language)
			{
				if(langnode['default'])
					setLanguageName = langnode.file;
				
				langnode.phrases = null; 
				languages[langnode.file] = langnode;
			}
			
			requestObject.removeEventListener(ResultEvent.RESULT, languagesLoaded);
			
			dispatchEvent(new LanguageEvent( LanguageEvent.LANGUAGE_UPDATED, setLanguageName ) );
			dispatchEvent(new LanguageEvent(LanguageEvent.LANGUAGES_LOADED, setLanguageName));
		}
		/**
		 * If selected language not loaded, load it. 
		 * @param event change language event
		 */
		private function loadLanguage(event:LanguageEvent):void
		{
			if(languages[event.currentLanguage].phrases == null)
			{
				requestObject.url = languagesDirectory + "/" + languages[event.currentLanguage].file ;
				requestObject.method = "get";
				requestObject.showStatusWindow = true;
				
				// Inform user about language loading
				if(currentLanguage == null)
					requestObject.statusText = 	"Language \""
												+ languages[event.currentLanguage].name
												+ "\" loading...";
				else
					requestObject.statusText = getPhraseFormat(
													"loading_language",
													languages[event.currentLanguage]
												);
				
				requestObject.addEventListener(ResultEvent.RESULT, languageLoaded);
				requestObject.send();
			
				currentLanguage = event.currentLanguage;
				
			}
			else
			{
			
				currentLanguage = event.currentLanguage;
				
				// Inform listeners that language loaded
				dispatchEvent(new LanguageEvent(LanguageEvent.LANGUAGE_LOADED, currentLanguage));
			}
		}
		
		/**
		 * Receives loaded language phrases
		 * @param event response event
		 */
		private function languageLoaded(event:ResultEvent):void
		{
			
			languages[currentLanguage].phrases = new Object();
			for each(var phrase:Object in event.result.language.phrase)
			{
				languages[currentLanguage].phrases[phrase.name] = StringUtil.trim(phrase.value);
			}
			
			
			requestObject.removeEventListener(ResultEvent.RESULT, languageLoaded);
			
			// Inform listeners that language loaded
			dispatchEvent(new LanguageEvent(LanguageEvent.LANGUAGE_LOADED, currentLanguage));
			
		}
		
		/**
		 * Changes currentLnaguage
		 * @param languageID language file
		 */
		public function changeLanguage(languageID:String):void
		{
			if(!languages[languageID]) return; // If language not exists
			if(currentLanguage == languageID) return; // Already loaded
			
			dispatchEvent(new LanguageEvent(LanguageEvent.LANGUAGE_UPDATED, languageID));	
		}
		
		/**
		 * Returns ArrayCollection of languages
		 */
		[Bindable(event='languagesLoaded')]
		public function get languagesList():ArrayCollection
		{
			_languageList = new ArrayCollection();
			for each(var language:Object in languages)
			{
				_languageList.addItem({
					'id':language.file,
					'label':language.name,
					'is_default':language['default']
				});
			}
			
			
			dispatchEvent(new LanguageEvent(LanguageEvent.LANGUAGES_LIST));
			
			return _languageList;
		}
		
		/**
		 * Returns index of current language in ArrayCollection
		 */
		 
		[Bindable(event='languagesList')]
		public function get lanaguageSelectedIndex():Number
		{
			for( var i:String in _languageList.source )
			{
				if(_languageList.source[i].is_default && currentLanguage == null)
					return Number(i);
				else if(_languageList.source[i].id == currentLanguage)
					return Number(i);
			}
			
			// No language selected? Why?
			return -1;
		}
	}
}