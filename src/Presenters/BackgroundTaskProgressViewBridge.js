var bridge = function (presenterPath)
{
    // Default the poll rate to 1 second.
    this.pollRate = 1000;

    window.gcd.core.mvp.viewBridgeClasses.HtmlViewBridge.apply( this, arguments );
};

bridge.prototype.onStateLoaded = function()
{
    if ( this.model.pollRate )
    {
        this.pollRate = this.model.pollRate * 1000;
    }
};

bridge.prototype = new window.gcd.core.mvp.viewBridgeClasses.HtmlViewBridge();
bridge.prototype.constructor = bridge;

bridge.prototype.attachEvents = function ()
{
    var self = this;

    this.progressNode = this.viewNode.querySelector( '.progress' );
    this.messageNode = this.viewNode.querySelector( '.message' );

    this.pollInterval = setInterval( function()
    {
        self.pollProgress();
    }, this.pollRate );
};

bridge.prototype.pollProgress = function()
{
    var self = this;

    this.raiseServerEvent( "GetProgress", function( response )
    {
        self.progressNode.style.width = response.percentageComplete + "%";
        self.messageNode.innerHTML = response.message;

        if ( response.percentageComplete >= 100 )
        {
            self.onComplete();
            self.raiseClientEvent( "OnComplete" );

            clearInterval( self.pollInterval );
        }
    });
};

bridge.prototype.onComplete = function()
{

};

window.gcd.core.mvp.viewBridgeClasses.BackgroundTaskProgressViewBridge = bridge;