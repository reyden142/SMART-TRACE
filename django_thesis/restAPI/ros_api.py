import ros_api

router = ros_api.Api('192.168.204.1')
r = router.talk('/system/identity/print')
print(r)