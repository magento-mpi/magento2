package magento.rpc
{
	import mx.containers.TitleWindow;
	import mx.managers.PopUpManager;
	import flash.display.DisplayObject;
	import mx.controls.ProgressBar;
	import mx.controls.ProgressBarLabelPlacement;
	
	/**
	 * Request status window
	 */
	public class RequestInfoWindow extends TitleWindow
	{
		/**
		 * Element that inform about request status
		 */
		private var loadingImage:ProgressBar = new ProgressBar();
		/**
		 * Object for center window in application
		 */
		private var parentElement:DisplayObject;
		
		/**
		 * Constructor
		 * @param parent  Object for center window in application
		 */
		public function RequestInfoWindow(parent:DisplayObject)
		{
			super();
			layout = "vertical"; 				
			loadingImage.indeterminate = true; 	
			loadingImage.labelPlacement = ProgressBarLabelPlacement.TOP; 
			addChild(loadingImage); 
			parentElement = parent;
		}
		
		/**
		 * Set the satus text
		 * @param value status text
		 */
		public function set statusLabel(value:String):void
		{
			loadingImage.label = value;	
		}
		
		/**
		 * Returns current status text
		 */
		public function get statusLabel():String
		{
			return loadingImage.label;
		}
		
		/**
		 * Show status window
		 */
		public function show():void
		{
			PopUpManager.addPopUp(this, parentElement , true); 
			PopUpManager.centerPopUp(this);  
		}
		
		/**
		 * Hide status window
		 */
		public function hide():void
		{
			PopUpManager.removePopUp(this);
		}

	}
}