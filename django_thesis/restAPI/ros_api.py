import ros_api

router = ros_api.Api('192.168.203.40')
r = router.talk('/system/identity/print')
print(r)