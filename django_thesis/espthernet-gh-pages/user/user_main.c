#include "freertos/FreeRTOS.h"
#include "freertos/task.h"
#include "freertos/timers.h"
#include "esp_wifi.h"
#include "esp_system.h"
#include "esp_event.h"
#include "esp_log.h"
#include "nvs_flash.h"
#include "esp_system.h"
#include "c_types.h"
#include "esp_wifi.h" //user_interface.h for esp8266 replaced with "esp_wifi.h" and "esp_system.h"
#include "esp_system.h"
#include "esp_system.h" //"ets_sys.h" for esp8266 replaced with "esp_system.h"
#include "driver/uart.h"
#include "uart_driver_install"
#include "osapi.h"
#include "espconn.h"
#include "mystuff.h"
#include "i2sduplex.h"
#include "commonservices.h"
#include "manchestrate.h"
#include <mdns.h>
#include <net_compat.h>
#include <iparpetc.h>

static const char *TAG = "esp32_ethernet";

#define PORT 7777

#define procTaskPrio        0
#define procTaskQueueLen    1

static volatile TimerHandle_t some_timer;

os_event_t    procTaskQueue[procTaskQueueLen];


static void ICACHE_FLASH_ATTR procTask(os_event_t *events)
{

	et_backend_tick_quick();

	CSTick( 0 );
	system_os_post(procTaskPrio, 0, 0 );
}

static void ICACHE_FLASH_ATTR myTimer(void *arg)
{
	et_backend_tick_slow();
	TickTCP();
	CSTick( 1 );
}

void ICACHE_FLASH_ATTR HandleUDP( uint16_t len )
{
	et_pop16(); //Discard checksum.  Already CRC32'd the ethernet.

	if( localport != 7878 )
		return;

	char  __attribute__ ((aligned (32))) retbuf[1300];
	int r = issue_command( retbuf, 1300, &ETbuffer[ETsendplace], len-8 );
	et_finish_callback_now();

	if( r > 0 )
	{
		//Using avrcraft, this is how you send "reply" UDP packets manually.
		et_startsend( 0x0000 );
		send_etherlink_header( 0x0800 );
		send_ip_header( 0x00, ipsource, 17 ); //UDP (will fill in size and checksum later)
		et_push16( localport );
		et_push16( remoteport );
		et_push16( 0 ); //length for later
		et_push16( 0 ); //csum for later

		ets_memcpy( &ETbuffer[ETsendplace], retbuf, r );
		ETsendplace += r;

		util_finish_udp_packet();
	}

}

void app_main(void)
{
    // Initialize NVS
    esp_err_t ret = nvs_flash_init();
    if (ret == ESP_ERR_NVS_NO_FREE_PAGES || ret == ESP_ERR_NVS_NEW_VERSION_FOUND) {
        ESP_ERROR_CHECK(nvs_flash_erase());
        ret = nvs_flash_init();
    }
    ESP_ERROR_CHECK(ret);

    // Initialize the TCP/IP stack
    tcpip_adapter_init();

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

    // Replace your task and timer initialization code using FreeRTOS functions
    xTaskCreate(procTask, "procTask", 2048, NULL, procTaskPrio, NULL);

    some_timer = xTimerCreate("some_timer", pdMS_TO_TICKS(10), pdTRUE, NULL, myTimer);
    xTimerStart(some_timer, 0);
}

static void procTask(void *pvParameters)
{
    while (1) {
        // Your task code here
        vTaskDelay(pdMS_TO_TICKS(1000)); // Example delay
    }
}

static void myTimer(TimerHandle_t xTimer)
{
    // Your timer callback code here
}

