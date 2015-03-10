var bridge = function (presenterPath)
{
    window.gcd.core.mvp.viewBridgeClasses.BackgroundTaskViewBridge.apply( this, arguments );
};

bridge.prototype = new window.gcd.core.mvp.viewBridgeClasses.BackgroundTaskViewBridge();
bridge.prototype.constructor = bridge;

bridge.prototype.onComplete = function()
{
    this.raisePostBackEvent("TaskComplete", this.model.BackgroundTaskStatusID );
};

window.gcd.core.mvp.viewBridgeClasses.BackgroundTaskFullFocusViewBridge = bridge;