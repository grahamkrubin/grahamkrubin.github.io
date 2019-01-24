import requests
import string
from time import sleep
import math
from .symbols.Generic import Generic
from .compat import text
from .compat import quote

user_agent = 'yahoo-ticker-symbol-downloader'
general_search_characters = 'abcdefghijklmnopqrstuvwxyz0123456789.='
first_search_characters = 'abcdefghijklmnopqrstuvwxyz'

class SymbolDownloader():
    """Abstract class"""

    def __init__(self, type, searchTerm):
        # All downloaded symbols are stored in a dict before exporting
        # This is to ensure no duplicate data
        self.companyName = searchTerm
        self.symbols = []
        self.rsession = requests.Session()
        self.type = type

        self.queries = []
        # self._add_queries()
        self.current_q = ""
        self.done = False
    # def search(self):
    #     # queries = []
    #     # queries.append(company_name)
    #     self.current_q = company_name
    #     self.nextRequest()

    # def _add_queries(self, prefix=''):
    #     # This method will add (prefix+)a...z to self.queries
    #     # This API requires the first character of the search to be a letter.
    #     # The second character can be a letter, number, dot, or equals sign.
    #     if len(prefix)==0:
    #         search_characters = first_search_characters
    #     else:
    #         search_characters = general_search_characters

    #     for i in range(len(search_characters)):
    #         element = str(prefix) + str(search_characters[i])
    #         if element not in self.queries:  # Avoid having duplicates in list
    #             self.queries.append(element)

    def _encodeParams(self, params):
        encoded = ''
        for key, value in params.items():
            encoded += ';' + quote(key) + '=' + quote(text(value))
        return encoded

    def _fetch(self, insecure):
        params = {
            'searchTerm': self.companyName,
        }
        query_string = {
            'device': 'console',
            'returnMeta': 'true',
        }
        protocol = 'http' if insecure else 'https'
        req = requests.Request('GET',
            protocol+'://finance.yahoo.com/_finance_doubledown/api/resource/searchassist'+self._encodeParams(params),
            headers={'User-agent': user_agent},
            params=query_string
        )
        req = req.prepare()
        # print("req " + req.url)
        resp = self.rsession.send(req, timeout=(12, 12))
        resp.raise_for_status()

        return resp.json()

    # def decodeSymbolsContainer(self, symbolsContainer):
    #     raise Exception("Function to extract symbols must be overwritten in subclass. Generic symbol downloader does not know how.")

    # def _getQueryIndex(self):
    #     return self.queries.index(self.current_q)

    # def getTotalQueries(self):
    #     return len(self.queries)

    # def _nextQuery(self):
    #     if self._getQueryIndex() + 1 >= len(self.queries):
    #         self.current_q = self.queries[0]
    #     else:
    #         self.current_q = self.queries[self._getQueryIndex() + 1]
    def decodeSymbolsContainer(self, json):
        # symbols = []
        # count = 0

        for row in json['data']['items']:
            ticker = str(row['symbol'])
            name = str(row['name'])
            # exchange = row['exch']
            # exchangeDisplay = row['exchDisp']
            # symbolType = row['type']
            # symbolTypeDisplay = row['typeDisp']
            self.symbols.append([ticker, name])

        # count = len(json['data']['items'])

        # return symbols

    # def getRowHeader(self):
    #     return SymbolDownloader.getRowHeader(self) + ["exchangeDisplay", "Type", "TypeDisplay"]



    def search(self, insecure=False, pandantic=False):
        # self._nextQuery()
        success = False
        retryCount = 0
        json = None
        # Eponential back-off algorithm
        # to attempt 5 more times sleeping 5, 25, 125, 625, 3125 seconds
        # respectively.
        maxRetries = 5
        while(success == False):
            try:
                json = self._fetch(insecure)
                success = True
            except (requests.HTTPError,
                    requests.exceptions.ChunkedEncodingError,
                    requests.exceptions.ReadTimeout,
                    requests.exceptions.ConnectionError) as ex:
                if retryCount < maxRetries:
                    attempt = retryCount + 1
                    sleepAmt = int(math.pow(5,attempt))
                    print("Retry attempt: " + str(attempt) + " of " + str(maxRetries) + "."
                        " Sleep period: " + str(sleepAmt) + " seconds."
                        )
                    sleep(sleepAmt)
                    retryCount = attempt
                else:
                    raise

        self.decodeSymbolsContainer(json)

    def isDone(self):
        return self.done

    def getCollectedSymbols(self):
        return self.symbols.values()

    def getRowHeader(self):
        return ["Ticker", "Name", "Exchange"]

    # def printProgress(self):
    #     if self.isDone():
    #         print("Progress: Done!")
    #     else:
    #         print("Progress:"
    #             + " Query " + str(self._getQueryIndex()+1) + "/" + str(self.getTotalQueries()) + "."
    #             + "\n"
    #             + str(len(self.symbols)) + " unique " + self.type + " entries collected so far."
    #             )
    #     print ("")
