#include "esp_system.h"

void my_reset_function() {
    // Perform a software reset
    esp_restart();
}

void app_main() {
    // Your application code here
   my_reset_function();
}

