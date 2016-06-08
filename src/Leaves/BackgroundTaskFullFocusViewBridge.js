var bridge = function (presenterPath)
{
    window.rhubarb.viewBridgeClasses.BackgroundTaskViewBridge.apply( this, arguments );
};

bridge.prototype = new window.rhubarb.viewBridgeClasses.BackgroundTaskViewBridge();
bridge.prototype.constructor = bridge;

bridge.prototype.onComplete = function()
{
    this.raisePostBackEvent("taskComplete", this.model.backgroundTaskStatusId );
};

window.rhubarb.viewBridgeClasses.BackgroundTaskFullFocusViewBridge = bridge;