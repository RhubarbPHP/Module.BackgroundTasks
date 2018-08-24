# Change log

### 2.0.1

* ADDED: onStart method to BackgroundTaskViewBridge 
* ADDED: OnStart client event raised after onStart is called in BackgroundTaskViewBridge
* UPDATED: onStart in BackgroundTaskProgressViewBridge: Immediately display progress at 0% with text "Please Wait ..."

### 2.0.0

* CHANGED: Moved to using ajax trigger with JSON streaming instead of process forking.   

### 1.1.2

* FIXED:    Fixed issue that caused the executing script to fail 
* ADDED:    Added the ProcessID that is currently being ran to the Background Task table

### 1.1.1
* UPDATED:  rhubarb_app environment setting support to allow background tasks to work in new environment

### 1.1.0
* UPDATED:	Stem 1.1 Support

### 1.0.0

* ADDED:    Codeception instead of PHP unit
* REMOVED:  Reference to the PhpIdeConfig - not in Rhubarb 1.0.0 any longer
