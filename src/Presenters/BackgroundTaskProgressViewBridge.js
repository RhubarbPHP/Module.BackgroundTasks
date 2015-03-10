var bridge = function (presenterPath)
{
    window.gcd.core.mvp.viewBridgeClasses.BackgroundTaskViewBridge.apply( this, arguments );
};

bridge.prototype = new window.gcd.core.mvp.viewBridgeClasses.BackgroundTaskViewBridge();
bridge.prototype.constructor = bridge;

bridge.prototype.attachEvents = function ()
{
    var self = this;

    this.progressNode = this.viewNode.querySelector( '.progress' );
    this.messageNode = this.viewNode.querySelector( '.message' );
};

bridge.prototype.onProgressReported = function(progress)
{
    this.progressNode.style.width = progress.percentageComplete + "%";
    this.messageNode.innerHTML = progress.message;
};

bridge.prototype.onComplete = function()
{
};

window.gcd.core.mvp.viewBridgeClasses.BackgroundTaskProgressViewBridge = bridge;