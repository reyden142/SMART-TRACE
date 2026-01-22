#include "freertos/FreeRTOS.h"
#include "freertos/task.h"
#include "freertos/timers.h"
#include "esp_wifi.h"
#include "esp_system.h"
#include "esp_event.h"
#include "esp_log.h"
#include "nvs_flash.h"
#include "driver/uart.h"
#include "esp_netif.h"

#include "freertos/FreeRTOS.h"
#include "freertos/task.h"
#include "freertos/timers.h"
#include "freertos/event_groups.h"
#include "esp_wifi.h"
#include "esp_log.h"
#include "nvs_flash.h"
#include "esp_netif.h"
#include "esp_http_server.h"


static const char *TAG = "esp32_ethernet";

#define PORT 7777

#define procTaskPrio        0
#define procTaskQueueLen    1

static volatile TimerHandle_t some_timer;

ip_event_t procTaskQueue[procTaskQueueLen]; // Change "os" to "ip"

// Function prototype for your task
static void procTask(void *pvParameters);

// Function prototype for your timer callback
static void myTimer(TimerHandle_t xTimer);

static uint16_t localport = 7878; // Define and set the local UDP port

void HandleUDP(uint16_t len)
{
    // Define and assign a value to localport
    uint16_t localport = 7878; // Change this to the appropriate local UDP port

    // Check if the received UDP packet is intended for this local port
    if (localport != 7878)
        return;


    // Rest of your UDP handling logic here

    char __attribute__((aligned(32))) retbuf[1300];

    // Implement your UDP handling logic here
}

void app_main(void)
{
    // Initialize NVS
    esp_err_t ret = nvs_flash_init();
    if (ret == ESP_ERR_NVS_NO_FREE_PAGES || ret == ESP_ERR_NVS_NEW_VERSION_FOUND)
    {
        ESP_ERROR_CHECK(nvs_flash_erase());
        ret = nvs_flash_init();
    }
    ESP_ERROR_CHECK(ret);

    // Initialize the TCP/IP stack
    ESP_ERROR_CHECK(esp_netif_init());

    // Initialize WiFi
    wifi_init_config_t cfg = WIFI_INIT_CONFIG_DEFAULT();
    ESP_ERROR_CHECK(esp_wifi_init(&cfg));
    ESP_ERROR_CHECK(esp_wifi_set_storage(WIFI_STORAGE_RAM));

    wifi_config_t wifi_config = {
        .sta = {
            .ssid = "PLDTHOMEFIBR5G45e08",
            .password = "Seces123456789!",
        },
    };
    ESP_ERROR_CHECK(esp_wifi_set_mode(WIFI_MODE_STA));
    ESP_ERROR_CHECK(esp_wifi_set_config(ESP_IF_WIFI_STA, &wifi_config));
    ESP_ERROR_CHECK(esp_wifi_start());

    // Other initialization code...

    // Create a task for procTask
    xTaskCreate(procTask, "procTask", 2048, NULL, procTaskPrio, NULL);

    // Create a timer and start it
    some_timer = xTimerCreate("some_timer", pdMS_TO_TICKS(10), pdTRUE, NULL, myTimer);
    xTimerStart(some_timer, 0);
}

static void procTask(void *pvParameters)
{
    while (1)
    {
        // Your task code here
        vTaskDelay(pdMS_TO_TICKS(1000)); // Example delay
    }
}

static void myTimer(TimerHandle_t xTimer)
{
    // Your timer callback code here
}

