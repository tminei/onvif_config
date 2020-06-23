from onvif import ONVIFCamera
import zeep


def zeep_pythonvalue(self, xmlvalue):
    return xmlvalue


zeep.xsd.simple.AnySimpleType.pythonvalue = zeep_pythonvalue
mycam = ONVIFCamera("192.168.1.10", "80", "admin", "admin1234", 'wsdl')

media_service = mycam.create_media_service()
media_service.GetProfiles()
media_profile = media_service.GetProfiles()[0]
token = media_profile["token"]
# media_service.GetStreamUri({'ProfileToken':token})

print(media_service.GetStreamUri({'StreamSetup':{'Stream':'RTP-Unicast','Transport':'UDP'},'ProfileToken':token}))
# print(media.GetStreamUri())
